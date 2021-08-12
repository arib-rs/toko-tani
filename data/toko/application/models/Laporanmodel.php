<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Laporanmodel extends CI_Model {

    function li_opname($gudang, $tanggal) {
        $q = $this->db->select('m_produk.*, m_supplier.*, SUM(stk_jumlah) as stok')
                ->from('m_produk')
                ->join('d_stok', 'prd_id = stk_prd_id')
                ->join('m_supplier', 'sup_id = prd_sup_id')
                ->where('stk_gdg_id', $gudang)
                ->where('stk_tanggal <=', $tanggal)
                ->group_by('prd_id')
                ->order_by('prd_nama')
                ->get()
                ->result_array();
        return $q;
    }

    function li_operasional($ts, $te) {
        $q = $this->db->from('d_kas')
                ->where('kas_jenis', 'pengeluaran')
                ->where("kas_tanggal BETWEEN '$ts' AND '$te'")
                ->order_by('kas_tanggal, kas_jam')
                ->get()
                ->result_array();
        return $q;
    }

    function li_penjualan($toko, $ts, $te) {
        $list = array();
        $qnota = $this->db->from('d_nota')
                ->join('m_customer', 'nota_tujuan = cus_id', 'left')
                ->where('nota_jenis', 'JUAL')
                ->where('nota_iskredit', 0)
                ->where('nota_asal', $toko)
                ->where("nota_tanggal BETWEEN '$ts' AND '$te'")
                ->order_by('nota_tanggal, nota_jam')
                ->get()
                ->result_array();
//        echo $this->db->last_query();
        $tglbefore = $notabefore = '';
        foreach ($qnota as $val) {
            $pemilik = ($val['cus_iskios']) ? '<br>' . $val['cus_pemilik'] : '';
            $customer = ($val['nota_tujuan'] == 0) ? 'Umum' : $val['cus_nama'] . $pemilik;
            $qdetail = $this->db->from('d_nota_detail')
                    ->join('m_produk', 'dtn_prd_id = prd_id')
                    ->where('dtn_nota_id', $val['nota_id'])
                    ->get()
                    ->result_array();
            foreach ($qdetail as $vd) {
                $temp = array(
                    'tanggal' => date('d/m/Y', strtotime($val['nota_tanggal'])),
                    'customer' => $customer,
                    'produk' => $vd['prd_nama'],
                    'hargabeli' => $vd['dtn_hargabeli'],
                    'hargajual' => $vd['dtn_hargajual'],
                    'jumlah' => $vd['dtn_jumlah'],
                );
                if ($val['nota_tanggal'] == $tglbefore) {
                    $temp['tanggal'] = NULL;
                }
                if ($val['nota_id'] == $notabefore) {
                    $temp['customer'] = NULL;
                }
                $list[] = $temp;
                $tglbefore = $val['nota_tanggal'];
                $notabefore = $val['nota_id'];
            }
        }
        return $list;
    }

    function li_penjualanproduk($idproduk, $ts, $te) {
        $q = $this->db->from('d_nota')
                ->join('m_customer', 'nota_tujuan = cus_id', 'left')
                ->join('d_nota_detail', 'dtn_nota_id = nota_id')
                ->join('m_produk', 'dtn_prd_id = prd_id')
                ->where('dtn_prd_id', $idproduk)
                ->where('nota_jenis', 'JUAL')
                ->where("nota_tanggal BETWEEN '$ts' AND '$te'")
                ->order_by('nota_tanggal, nota_jam')
                ->get()
                ->result_array();
        return $q;
    }

    function li_penjualanppn($jenis, $toko, $ts, $te) {
        $q = $this->db->select('prd_id, prd_nama, prd_satuan, SUM(dtn_jumlah) as jumlah, SUM(dtn_hargajual*dtn_jumlah) as total')
                ->from('d_nota')
                ->join('d_nota_detail', 'dtn_nota_id = nota_id')
                ->join('m_produk', 'dtn_prd_id = prd_id')
                ->where('prd_isppn', $jenis)
                ->where('nota_asal', $toko)
                ->where('nota_jenis', 'JUAL')
                ->where("nota_tanggal BETWEEN '$ts' AND '$te'")
                ->group_by('prd_id')
                ->get()
                ->result_array();
//        echo $this->db->last_query();
        return $q;
    }

}
