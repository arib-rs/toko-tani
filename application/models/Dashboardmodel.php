<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboardmodel extends CI_Model {

    function get_kas($start, $end, $rekening) {
        $q = $this->db->select('kas_tanggal, SUM(kas_debet) AS debet, SUM(kas_kredit) AS kredit')
                ->from('d_kas')
                ->where("kas_tanggal BETWEEN '$start' AND '$end'")
                ->where('kas_rekening', $rekening)
                ->group_by('kas_tanggal')
                ->order_by('kas_tanggal')
                ->get();
        return $q->result_array();
    }
    
    function get_box($start, $end, $rekening) {
        $penjualan = $this->db->from('d_nota')
                ->where("nota_tanggal BETWEEN '$start' AND '$end'")
                ->where('nota_jenis', 'JUAL')
                ->where('nota_iskredit', 0)
                ->get()
                ->num_rows();
        $penjualanbl = $this->db->from('d_nota')
                ->where("nota_tanggal BETWEEN '$start' AND '$end'")
                ->where('nota_jenis', 'JUAL')
                ->where('nota_iskredit', 1)
                ->get()
                ->num_rows();
        $pembelian = $this->db->from('d_nota')
                ->where("nota_tanggal BETWEEN '$start' AND '$end'")
                ->where('nota_jenis', 'BELI')
                ->get()
                ->num_rows();
        $customer = $this->db->get('m_customer')->num_rows();
        $arr = array(
            'penjualan' => $penjualan,
            'penjualanbl' => $penjualanbl,
            'pembelian' => $pembelian,
            'customer' => $customer
        );
        return $arr;
    }

}
