<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Keuanganmodel extends CI_Model
{

    function sv_saldo($data)
    {
        if ($data['sld_id'] == '') {
            $data['sld_id'] = $this->ascfunc->newid_('d_saldo', 'sld_id');
            $q = $this->db->insert('d_saldo', $data);
        } else {
            $q = $this->db->where('sld_id', $data['sld_id'])
                ->update('d_saldo', $data);
        }
        return $q;
    }

    function li_hutangsupplier($tahun)
    {
        $data = $this->db->order_by('sup_nama')->get('m_supplier');
        $list = array();
        foreach ($data->result_array() as $val) {
            $hutang = $this->get_hutangsupplier($val['sup_id'], $tahun);
            $detailhutang = $this->get_detailhutangsupplier($val['sup_id'], $tahun);
            if ($hutang <= 0) {
                continue;
            }
            $temp = $val;
            $temp['hutang'] = $hutang;
            $temp['detail'] = $detailhutang;
            $list[] = $temp;
        }
        return $list;
    }

    function get_hutangsupplier($id, $tahun)
    {
        $nota = $this->db->select('a.*,(SELECT SUM(dtn_jumlah*dtn_hargabeli) FROM d_nota_detail b WHERE b.dtn_nota_id = a.nota_id GROUP BY b.dtn_nota_id) AS total')
            ->from('d_nota a')
            ->where('nota_jenis', 'BELI')
            ->where('YEAR(nota_tanggal)', $tahun)
            ->where('nota_asal', $id)
            ->where('nota_iskredit', 1)
            ->get();
        $kredit = 0;
        foreach ($nota->result_array() as $val) {
            $kredit += $val['total'] + ($val['total'] * $val['nota_ppn'] / 100);
        }
        $pembayaran = $this->db->select('SUM(pmb_nominal) as total')
            ->from('d_pembayaran')
            ->where(array('pmb_sup_id' => $id, 'YEAR(pmb_tanggal)' => $tahun))
            ->get()
            ->row_array();
        $debet = abs($pembayaran['total']);
        return $kredit - $debet;
    }

    function get_detailhutangsupplier($id, $tahun)
    {
        $this->load->model('Transaksimodel');


        $nota = $this->db->select('a.*,(SELECT SUM(dtn_jumlah*dtn_hargabeli) FROM d_nota_detail b WHERE b.dtn_nota_id = a.nota_id GROUP BY b.dtn_nota_id) AS total')
            ->from('d_nota a')
            ->where('nota_jenis', 'BELI')
            ->where('YEAR(nota_tanggal)', $tahun)
            ->where('nota_asal', $id)
            ->where('nota_iskredit', 1)
            ->get();
        $arr = array();
        foreach ($nota->result_array() as $val) {
            $total = $val['total'] - $val['nota_diskon'];
            $kredit = $total + ($total * $val['nota_ppn'] / 100);
            $pembayaran = $this->db->select('SUM(pmb_nominal) as total')
                ->from('d_pembayaran')
                ->where(array('pmb_nota_id' => $val['nota_id']))
                ->get()
                ->row_array();
            $debet = abs($pembayaran['total']);
            if (($kredit - $debet) <= 0) {
                continue;
            }
            $dtnota = $this->Transaksimodel->get_ntbm($val['nota_id']);
            $arr[] = array(
                'id' => $val['nota_id'],
                'ppn' => $val['nota_ppn'],
                'tanggal' => date('d/m/Y', strtotime($val['nota_tanggal'])) . ' ' . date('H:i', strtotime($val['nota_jam'])),
                'detail' =>  $dtnota['detail'],
                'hutang' => $kredit - $debet
            );
        }
        return $arr;
    }

    function sv_pembayaransupplier($data, $rekening)
    {
        $supplier = $this->db->get_where('m_supplier', array('sup_id' => $data['pmb_sup_id']))->row_array();
        if ($data['pmb_id'] == '') {
            $data['pmb_id'] = $this->ascfunc->newid_('d_pembayaran', 'pmb_id');
            $q = $this->db->insert('d_pembayaran', $data);
            $kas = array(
                'kas_id' => 'BYRS' . $data['pmb_id'],
                'kas_rekening' => $rekening,
                'kas_uraian' => 'Pembayaran ke ' . $supplier['sup_nama'],
                'kas_keterangan' => NULL,
                'kas_tanggal' => $data['pmb_tanggal'],
                'kas_jam' => $data['pmb_jam'],
                'kas_debet' => NULL,
                'kas_kredit' => $data['pmb_nominal'],
                'kas_jenis' => 'pembayaran'
            );
            $this->db->insert('d_kas', $kas);
        } else {
            $q = $this->db->where('pmb_id', $data['pmb_id'])
                ->update('d_pembayaran', $data);
            $kas = array(
                'kas_rekening' => $rekening,
                'kas_uraian' => 'Pembayaran ke ' . $supplier['sup_nama'],
                'kas_keterangan' => NULL,
                'kas_tanggal' => $data['pmb_tanggal'],
                'kas_jam' => $data['pmb_jam'],
                'kas_debet' => NULL,
                'kas_kredit' => $data['pmb_nominal'],
                'kas_jenis' => 'pembayaran'
            );
            $this->db->where('kas_id', 'BYRS' . $data['pmb_id'])
                ->update('d_kas', $kas);
        }
        return $q;
    }

    function get_pembayaransupplier($id)
    {
        $q = $this->db->from('d_pembayaran')
            ->where('pmb_id', $id)
            ->get()
            ->row_array();
        $kas = $this->db->select('kas_rekening')
            ->from('d_kas')
            ->where('kas_id', 'BYRS' . $id)
            ->get()
            ->row_array();
        $sisa = $this->get_hutangsupplier($id, date('Y', strtotime($q['pmb_tanggal'])));
        $data = $q;
        $data['rekening'] = $kas['kas_rekening'];
        $data['hutang'] = $sisa + $q['pmb_nominal'];
        $data['fhutang'] = $this->ascfunc->nf_($data['hutang']);
        return $data;
    }

    function sspntpembayaran($aColumns, $sWhere, $sLimit, $bulan, $tahun)
    {
        if ($tahun == '' && $bulan == '') {
            $where = '';
        } else {
            $where = "WHERE YEAR(pmb_tanggal) = $tahun AND MONTH(pmb_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT * FROM (
            SELECT * FROM d_pembayaran 
            JOIN m_supplier ON pmb_sup_id = sup_id 
            $where 
            ORDER BY pmb_tanggal desc, pmb_jam desc
        ) A
        $sWhere
        $sLimit");
        return $query->result_array();
    }

    function sspntpembayaran_total($sIndexColumn, $sWhere, $bulan, $tahun)
    {
        if ($tahun == '' && $bulan == '') {
            $where = '';
        } else {
            $where = "WHERE YEAR(pmb_tanggal) = $tahun AND MONTH(pmb_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT $sIndexColumn FROM (
            SELECT * FROM d_pembayaran 
            $where 
            ORDER BY pmb_tanggal desc, pmb_jam desc
        ) A
        $sWhere");
        return $query->num_rows();
    }

    function del_pembayaran($id)
    {
        $dt = $this->db->get_where('d_pembayaran', array('pmb_id' => $id))->row_array();
        $q = $this->db->where('pmb_id', $id)
            ->delete('d_pembayaran');
        if ($q) {
            $prefix = ($dt['pmb_sup_id'] == '') ? 'BYRC' : 'BYRS';
            $this->db->where('kas_id', $prefix . $id)
                ->delete('d_kas');
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function get_piutangcustomer($id, $tahun)
    {
        $nota = $this->db->select('a.*,(SELECT SUM(dtn_jumlah*dtn_hargajual) FROM d_nota_detail b WHERE b.dtn_nota_id = a.nota_id GROUP BY b.dtn_nota_id) AS total')
            ->from('d_nota a')
            ->where('nota_jenis', 'JUAL')
            ->where('YEAR(nota_tanggal)', $tahun)
            ->where('nota_tujuan', $id)
            ->where('nota_iskredit', 1)
            ->get();
        $div = '<div class="radio"><label><input type="radio" name="idnota" value="0" data-sisa="0" checked="">-Pilih Nota-</label></div>';
        foreach ($nota->result_array() as $val) {
            $pembayaran = $this->db->select('SUM(pmb_nominal) as total')
                ->from('d_pembayaran')
                ->where(array('pmb_nota_id' => $val['nota_id']))
                ->get()
                ->row_array();
            $total = $val['total'] - $val['nota_diskon'];
            $gtotal = $total + ($val['total'] * $val['nota_ppn'] / 100);
            $kredit = $gtotal - $pembayaran['total'];
            if ($kredit == 0) {
                continue;
            }
            $div .= '<div class="radio"><label><input type="radio" name="idnota" value="' . $val['nota_id'] . '" data-sisa="' . $kredit . '">Kode Nota : ' . $val['nota_id'] . ' (Sisa Piutang Rp. ' . $this->ascfunc->nf_($kredit) . ')</label></div>';
        }
        return $div;
    }

    function sv_piutang($data, $rekening)
    {
        $customer = $this->db->select('m_customer.cus_nama')
            ->from('d_nota')
            ->join('m_customer', 'nota_tujuan = cus_id')
            ->where(array('nota_id' => $data['pmb_nota_id']))
            ->get()
            ->row_array();
        if ($data['pmb_id'] == '') {
            $data['pmb_id'] = $this->ascfunc->newid_('d_pembayaran', 'pmb_id');
            $q = $this->db->insert('d_pembayaran', $data);
            $kas = array(
                'kas_id' => 'BYRC' . $data['pmb_id'],
                'kas_rekening' => $rekening,
                'kas_uraian' => 'Pembayaran dari Customer ' . $customer['cus_nama'],
                'kas_keterangan' => NULL,
                'kas_tanggal' => $data['pmb_tanggal'],
                'kas_jam' => $data['pmb_jam'],
                'kas_debet' => $data['pmb_nominal'],
                'kas_kredit' => NULL,
                'kas_jenis' => 'piutang'
            );
            $this->db->insert('d_kas', $kas);
            return $q;
        } else {
            return FALSE;
        }
    }

    function sspntpiutang($aColumns, $sWhere, $sLimit, $bulan, $tahun)
    {
        if ($tahun == '' && $bulan == '') {
            $where = '';
        } else {
            $where = "WHERE YEAR(pmb_tanggal) = $tahun AND MONTH(pmb_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT * FROM (
            SELECT d_pembayaran.*, m_customer.*  FROM d_pembayaran 
            JOIN d_nota ON pmb_nota_id = nota_id
            JOIN m_customer ON nota_tujuan = cus_id 
            $where 
            ORDER BY pmb_tanggal desc, pmb_jam desc
        ) A
        $sWhere
        $sLimit");
        return $query->result_array();
    }

    function sspntpiutang_total($sIndexColumn, $sWhere, $bulan, $tahun)
    {
        if ($tahun == '' && $bulan == '') {
            $where = '';
        } else {
            $where = "WHERE YEAR(pmb_tanggal) = $tahun AND MONTH(pmb_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT $sIndexColumn FROM (
            SELECT * FROM d_pembayaran 
            $where 
            ORDER BY pmb_tanggal desc, pmb_jam desc
        ) A
        $sWhere");
        return $query->num_rows();
    }

    function li_piutangcustomer($tahun)
    {
        $data = $this->db->order_by('cus_nama')->get('m_customer');
        $list = array();
        foreach ($data->result_array() as $val) {
            $piutang = $this->get_detailpiutang($val['cus_id'], $tahun);
            if ($piutang <= 0) {
                continue;
            }
            $temp = $val;
            $temp['detail'] = $piutang;
            $list[] = $temp;
        }
        return $list;
    }
    function li_piutangcustomerdashboard()
    {
        $data = $this->db->order_by('cus_nama')->get('m_customer');
        $list = array();
        foreach ($data->result_array() as $val) {
            $piutang = $this->get_detailpiutangdashboard($val['cus_id']);
            if (count($piutang) <= 0) {
                continue;
            }
            $temp = $val;
            $temp['detail'] = $piutang;
            $list[] = $temp;
        }
        return $list;
    }

    function get_piutang($id, $tahun)
    {
        $nota = $this->db->select('a.*,(SELECT SUM(dtn_jumlah*dtn_hargajual) FROM d_nota_detail b WHERE b.dtn_nota_id = a.nota_id GROUP BY b.dtn_nota_id) AS total')
            ->from('d_nota a')
            ->where('nota_jenis', 'JUAL')
            ->where('YEAR(nota_tanggal)', $tahun)
            ->where('nota_tujuan', $id)
            ->where('nota_iskredit', 1)
            ->get();
        $debet = $kredit = 0;
        foreach ($nota->result_array() as $val) {
            $total = $val['total'] - $val['nota_diskon'];
            $kredit += $total + ($total * $val['nota_ppn'] / 100);
            $pembayaran = $this->db->select('SUM(pmb_nominal) as total')
                ->from('d_pembayaran')
                ->where(array('pmb_nota_id' => $val['nota_id']))
                ->get()
                ->row_array();
            $debet += abs($pembayaran['total']);
        }
        return $kredit - $debet;
    }
    function get_piutang_bynota($id)
    {
        $nota = $this->db->select('a.*,(SELECT SUM(dtn_jumlah*dtn_hargajual) FROM d_nota_detail b WHERE b.dtn_nota_id = a.nota_id GROUP BY b.dtn_nota_id) AS total')
            ->from('d_nota a')
            ->where('nota_jenis', 'JUAL')
            // ->where('YEAR(nota_tanggal)', $tahun)
            ->where('nota_id', $id)
            ->where('nota_iskredit', 1)
            ->get();
        $debet = $kredit = 0;
        foreach ($nota->result_array() as $val) {
            $total = $val['total'] - $val['nota_diskon'];
            $kredit += $total + ($total * $val['nota_ppn'] / 100);
            $pembayaran = $this->db->select('SUM(pmb_nominal) as total')
                ->from('d_pembayaran')
                ->where(array('pmb_nota_id' => $val['nota_id']))
                ->get()
                ->row_array();
            $debet += abs($pembayaran['total']);
        }
        return $kredit - $debet;
    }

    function get_detailpiutang($id, $tahun)
    {
        $nota = $this->db->select('a.*,(SELECT SUM(dtn_jumlah*dtn_hargajual) FROM d_nota_detail b WHERE b.dtn_nota_id = a.nota_id GROUP BY b.dtn_nota_id) AS total')
            ->from('d_nota a')
            ->where('nota_jenis', 'JUAL')
            ->where('YEAR(nota_tanggal)', $tahun)
            ->where('nota_tujuan', $id)
            ->where('nota_iskredit', 1)
            ->get();
        $arr = array();
        foreach ($nota->result_array() as $val) {
            $total = $val['total'] - $val['nota_diskon'];
            $kredit = $total + ($total * $val['nota_ppn'] / 100);
            $pembayaran = $this->db->select('SUM(pmb_nominal) as total')
                ->from('d_pembayaran')
                ->where(array('pmb_nota_id' => $val['nota_id']))
                ->get()
                ->row_array();
            $debet = abs($pembayaran['total']);
            if (($kredit - $debet) <= 0) {
                continue;
            }
            $arr[] = array(
                'id' => $val['nota_no'],
                'tanggal' => date('d/m/Y', strtotime($val['nota_tanggal'])) . ' ' . date('H:i', strtotime($val['nota_jam'])),
                'piutang' => $kredit - $debet
            );
        }
        return $arr;
    }

    function get_detailpiutangdashboard($id)
    {
        $now = date('Y-m-d', strtotime(date('Y-m-d')));


        $nota = $this->db->select('a.*,(SELECT SUM(dtn_jumlah*dtn_hargajual) FROM d_nota_detail b WHERE b.dtn_nota_id = a.nota_id GROUP BY b.dtn_nota_id) AS total')
            ->from('d_nota a')
            ->where('nota_jenis', 'JUAL')
            // ->where('YEAR(nota_tanggal)', $tahun)
            ->where('nota_tujuan', $id)
            ->where('nota_iskredit', 1)
            ->get();
        $arr = array();
        foreach ($nota->result_array() as $val) {
            $endjatuhtempo = date('Y-m-d', strtotime($val['nota_tanggal'] . ' + 1 month'));
            $startjatuhtempo = date('Y-m-d', strtotime($endjatuhtempo . ' - 7 days'));
            //if ($now <= $endjatuhtempo && $now > $startjatuhtempo) {
                $total = $val['total'] - $val['nota_diskon'];
                $kredit = $total + ($total * $val['nota_ppn'] / 100);
                $pembayaran = $this->db->select('SUM(pmb_nominal) as total')
                    ->from('d_pembayaran')
                    ->where(array('pmb_nota_id' => $val['nota_id']))
                    ->get()
                    ->row_array();
                $debet = abs($pembayaran['total']);
                if (($kredit - $debet) <= 0) {
                    continue;
                }
                $arr[] = array(
                    'id' => $val['nota_no'],
                    'tanggal' => date('d/m/Y', strtotime($val['nota_tanggal'])) . ' ' . date('H:i', strtotime($val['nota_jam'])),
                    'jatuhtempo' => date('d/m/Y', strtotime($endjatuhtempo)),
                    // 'startjatuhtempo' => date('d/m/Y', strtotime($startjatuhtempo)),
                    'piutang' => $kredit - $debet
                );
            //}
        }
        return $arr;
    }
}
