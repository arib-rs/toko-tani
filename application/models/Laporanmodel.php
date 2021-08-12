<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Laporanmodel extends CI_Model
{

    function li_opname($gudang, $tanggal)
    {
        $q = $this->db->select('m_produk.*, m_supplier.*, SUM(stk_jumlah) as stok,stk_kadaluarsa,stk_nobatch')
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

    function li_operasional($ts, $te)
    {
        $q = $this->db->from('d_kas')
            ->where('kas_jenis', 'pengeluaran')
            ->where("kas_tanggal BETWEEN '$ts' AND '$te'")
            ->order_by('kas_tanggal, kas_jam')
            ->get()
            ->result_array();
        return $q;
    }

    function li_penjualan_admin($admin, $ts, $te)
    {
        $this->load->model('Keuanganmodel');
        $list = array();
        $qnota = $this->db->from('d_nota')
            ->join('m_customer', 'nota_tujuan = cus_id', 'left')
            ->where('nota_jenis', 'JUAL')
            ->where('nota_cb', $admin)
            ->where("nota_tanggal BETWEEN '$ts' AND '$te'")
            ->order_by('nota_tanggal, nota_jam')
            ->get()
            ->result_array();
        //        echo $this->db->last_query();
        $tglbefore = $notabefore = '';
        foreach ($qnota as $val) {
            $pemilik = ($val['cus_iskios']) ? '<br>' . $val['cus_pemilik'] : '';
            $customer = ($val['nota_tujuan'] == 0) ? 'Umum' : $val['cus_nama'] . $pemilik;
            $kredit = $this->Keuanganmodel->get_piutang_bynota($val['nota_id']);
            $qdetail = $this->db->from('d_nota_detail')
                ->join('m_produk', 'dtn_prd_id = prd_id')
                ->where('dtn_nota_id', $val['nota_id'])
                ->get()
                ->result_array();
            foreach ($qdetail as $vd) {
                $temp = array(
                    'tanggal' => date('d/m/Y', strtotime($val['nota_tanggal'])),
                    'nota' => $val['nota_id'],
                    'nonota' => $val['nota_no'],
                    'cus_id' => $val['cus_id'],
                    'customer' => $customer,
                    'produk' => $vd['prd_nama'],
                    'hargabeli' => $vd['dtn_hargabeli'],
                    'hargajual' => $vd['dtn_hargajual'],
                    'kredit' => $kredit == 0 ? 0 : 1,
                    'jumlah' => $vd['dtn_jumlah'],
                );
                if ($val['nota_tanggal'] == $tglbefore) {
                    $temp['tanggal'] = NULL;
                }
                if ($val['nota_id'] == $notabefore) {
                    $temp['customer'] = NULL;
                    $temp['nota'] = NULL;
                }
                $list[] = $temp;
                $tglbefore = $val['nota_tanggal'];
                $notabefore = $val['nota_id'];
            }
        }
        return $list;
    }
    function li_penjualan_admin_excel($admin, $ts, $te)
    {
        $this->load->model('Keuanganmodel');
        $list = $bulan = array();
        $blnbefore = '';

        $qnota = $this->db->from('d_nota_detail')
            ->join('d_nota', 'dtn_nota_id = nota_id', 'left')
            ->join('m_produk', 'dtn_prd_id = prd_id', 'left')
            ->where('nota_jenis', 'JUAL')
            ->where('nota_cb', $admin)
            ->where("nota_tanggal BETWEEN '$ts' AND '$te'")
            ->order_by('nota_tanggal, nota_jam')
            ->get()
            ->result_array();

        foreach ($qnota as $val) {

            $blnnow = date('m-Y', strtotime($val['nota_tanggal']));

            if (!isset($list[$val['dtn_prd_id']])) {
                $list[$val['dtn_prd_id']] = array(
                    'produk' => $val['prd_nama'],
                    'harga' => $val['dtn_hargajual']
                );
            }

            if (isset($list[$val['dtn_prd_id']]['data'][$blnnow]['jumlah'])) {
                $list[$val['dtn_prd_id']]['data'][$blnnow]['jumlah'] += $val['dtn_jumlah'];
            } else {
                $list[$val['dtn_prd_id']]['data'][$blnnow]['jumlah'] = $val['dtn_jumlah'];
            }
            $list[$val['dtn_prd_id']]['data'][$blnnow]['total'] = $list[$val['dtn_prd_id']]['data'][$blnnow]['jumlah'] * $list[$val['dtn_prd_id']]['harga'];

            if ($blnbefore != $blnnow) {
                $bulan[] = array(
                    'bulan' => date('m', strtotime($val['nota_tanggal'])),
                    'tahun' => date('Y', strtotime($val['nota_tanggal']))
                );
                $blnbefore = $blnnow;
            }
        }
        $return['list'] = $list;
        $return['bulan'] = $bulan;
        // $return['q'] = $qnota;
        return $return;
    }
    function li_penjualan($toko, $ts, $te)
    {
        $this->load->model('Keuanganmodel');
        $list = array();
        $qnota = $this->db->from('d_nota')
            ->join('m_customer', 'nota_tujuan = cus_id', 'left')
            ->where('nota_jenis', 'JUAL')
            //->where('nota_iskredit', 0)
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
            $kredit = $this->Keuanganmodel->get_piutang_bynota($val['nota_id']);
            $qdetail = $this->db->from('d_nota_detail')
                ->join('m_produk', 'dtn_prd_id = prd_id')
                ->where('dtn_nota_id', $val['nota_id'])
                ->get()
                ->result_array();
            foreach ($qdetail as $vd) {
                $temp = array(
                    'tanggal' => date('d/m/Y', strtotime($val['nota_tanggal'])),
                    'nota' => $val['nota_id'],
                    'cus_id' => $val['cus_id'],
                    'customer' => $customer,
                    'produk' => $vd['prd_nama'],
                    'hargabeli' => $vd['dtn_hargabeli'],
                    'hargajual' => $vd['dtn_hargajual'],
                    'kredit' => $kredit == 0 ? 0 : 1,
                    'jumlah' => $vd['dtn_jumlah'],
                );
                if ($val['nota_tanggal'] == $tglbefore) {
                    $temp['tanggal'] = NULL;
                }
                if ($val['nota_id'] == $notabefore) {
                    $temp['customer'] = NULL;
                    $temp['nota'] = NULL;
                }
                $list[] = $temp;
                $tglbefore = $val['nota_tanggal'];
                $notabefore = $val['nota_id'];
            }
        }
        return $list;
    }
    function li_pembelian($supplier, $ts, $te)
    {

        $list = array();
        $qnota = '';
        if ($supplier != '') {
            $qnota = $this->db->from('d_nota')
                ->join('m_supplier', 'nota_asal = sup_id', 'left')
                ->where('nota_jenis', 'BELI')
                //->where('nota_iskredit', 0)
                ->where('nota_asal', $supplier)
                ->where("nota_tanggal BETWEEN '$ts' AND '$te'")
                ->order_by('nota_tanggal, nota_jam')
                ->get()
                ->result_array();
        } else {
            $qnota = $this->db->from('d_nota')
                ->join('m_supplier', 'nota_asal = sup_id', 'left')
                ->where('nota_jenis', 'BELI')
                //->where('nota_iskredit', 0)
                // ->where('nota_asal', $supplier)
                ->where("nota_tanggal BETWEEN '$ts' AND '$te'")
                ->order_by('nota_tanggal, nota_jam')
                ->get()
                ->result_array();
        }
        //        echo $this->db->last_query();
        $tglbefore = $notabefore = '';
        foreach ($qnota as $val) {
            $qdetail = $this->db->from('d_nota_detail')
                ->join('m_produk', 'dtn_prd_id = prd_id')
                ->where('dtn_nota_id', $val['nota_id'])
                ->get()
                ->result_array();
            foreach ($qdetail as $vd) {
                $temp = array(
                    'tanggal' => date('d/m/Y', strtotime($val['nota_tanggal'])),
                    'nota' => $val['nota_id'],
                    'supplier' => $val['sup_nama'],
                    'satuan' => $vd['prd_satuan'],
                    'produk' => $vd['prd_nama'],
                    'hargabeli' => $vd['dtn_hargabeli'],
                    'kredit' => $val['nota_iskredit'],
                    // 'hargajual' => $vd['dtn_hargajual'],
                    'jumlah' => $vd['dtn_jumlah'],
                    'ppn' => $val['nota_ppn'],
                );
                if ($val['nota_tanggal'] == $tglbefore) {
                    $temp['tanggal'] = NULL;
                }
                if ($val['nota_id'] == $notabefore) {
                    $temp['supplier'] = NULL;
                    $temp['nota'] = NULL;
                }
                $list[] = $temp;
                $tglbefore = $val['nota_tanggal'];
                $notabefore = $val['nota_id'];
            }
        }
        return $list;
    }

    function li_penjualanproduk($idproduk, $ts, $te)
    {
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

    function li_penjualanppn($jenis, $toko, $ts, $te)
    {
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
