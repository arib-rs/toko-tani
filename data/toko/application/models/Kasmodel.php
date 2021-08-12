<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Kasmodel extends CI_Model {

    var $prefix = array(
        'obm' => 'OBM',
        'prive' => 'PRV',
        'kaskecil' => 'KSK',
        'notifikasi' => 'NTF',
        'lainnya' => 'LNN',
        'pajak' => 'PJK',
        'pengeluaran' => 'KLR'
    );

    function sv_kas($data) {
        if ($data['kas_id'] == '') {
            $this->load->helper('string');
            $data['kas_id'] = $this->prefix[$data['kas_jenis']] . random_string('alnum', 17);
            $q = $this->db->insert('d_kas', $data);
        } else {
            $q = $this->db->where('kas_id', $data['kas_id'])
                    ->update('d_kas', $data);
        }
        return $q;
    }

    function ssplist_($aColumns, $sWhere, $sLimit, $jenis, $bulan, $tahun) {
        if ($tahun == '' && $bulan == '') {
            $where = '';
        } else {
            $where = "AND YEAR(kas_tanggal) = $tahun AND MONTH(kas_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT * FROM (
            SELECT * FROM d_kas 
            WHERE kas_jenis = '$jenis' $where 
            ORDER BY kas_tanggal, kas_jam
        ) A
        $sWhere
        $sLimit");
        return $query->result_array();
    }

    function ssplist_total($sIndexColumn, $sWhere, $jenis, $bulan, $tahun) {
        if ($tahun == '' && $bulan == '') {
            $where = '';
        } else {
            $where = "AND YEAR(kas_tanggal) = $tahun AND MONTH(kas_tanggal) = " . abs($bulan);
        }
        $query = $this->db->query("SELECT $sIndexColumn FROM (
            SELECT * FROM d_kas 
            WHERE kas_jenis = '$jenis' $where 
            ORDER BY kas_tanggal, kas_jam
        ) A
        $sWhere");
        return $query->num_rows();
    }

    function rekapkas($tm, $ta, $rekening) {
        $data = array();
        $begin = new DateTime($tm);
        $end = new DateTime($ta);
        for ($i = $begin; $begin <= $end; $i->modify('+1 day')) {
            $q = $this->db->from('d_kas')
                    ->where('kas_tanggal', $i->format("Y-m-d"))
                    ->where('kas_rekening', $rekening)
                    ->order_by('kas_uraian')
                    ->get();
            if ($q->num_rows() > 0) {
                $ind = TRUE;
                foreach ($q->result_array() as $val) {
                    $temp = $val;
                    $temp['tgl'] = ($ind) ? $i->format("d") : '';
                    array_push($data, $temp);
                    $ind = FALSE;
                }
            }
        }
        return $data;
    }

    function saldoawal($tm, $rekening) {
        $tahun = date('Y', strtotime($tm));
        $bftm = new DateTime($tm);
        $bftm->modify('-1 day');
        $beforetm = $bftm->format("Y-m-d");

        $saldoawal = $this->db->from('d_saldo')
                ->where('sld_rekening', $rekening)
                ->where('sld_tahun', $tahun)
                ->get()
                ->row_array();
        if (date('n', strtotime($tm)) == 1) {
            $kasbefore['debet'] = $kasbefore['kredit'] = 0;
        } else {
            $kasbefore = $this->db->select('SUM(kas_debet) AS debet, SUM(kas_kredit) AS kredit')
                    ->from('d_kas')
                    ->where('kas_rekening', $rekening)
                    ->where("kas_tanggal BETWEEN '$tahun-01-01' AND '$beforetm'")
                    ->get()
                    ->row_array();
        }
        $sisa = $saldoawal['sld_debet'] - $saldoawal['sld_kredit'] + $kasbefore['debet'] - $kasbefore['kredit'];
        if ($sisa < 0) {
            $data = array(
                'debet' => NULL,
                'kredit' => abs($sisa)
            );
        } else {
            $data = array(
                'debet' => $sisa,
                'kredit' => NULL
            );
        }
        return $data;
    }

}
