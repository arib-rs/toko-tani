<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ascfunc {

    var $db;
    var $CI;
    var $sys = 'zx_xvrty';
    var $bulan_ = array(
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    );
    var $harga_ = array(
        'hargakhusus' => 'Harga Khusus',
        'hargamember' => 'Harga Anggota',
        'hargagrosir' => 'Harga Grosir',
        'hargaecer' => 'Harga Eceran'
    );

    public function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->database();
        $this->CI->load->library('session');
    }

    function header_($menu = '') {
        $header = array(
            'id' => $this->CI->session->userdata('log_id'),
            'level' => $this->CI->session->userdata('log_level'),
            'nama' => $this->CI->session->userdata('log_nama'),
            'active' => $menu
        );
        $header['info'] = $this->info_();
        return $header;
    }

    function info_() {
        $sql = $this->CI->db->get_where('m_sistem', array('sis_about' => 'info'));
        foreach ($sql->result_array() as $data) {
            $i = str_replace('inf_', '', $data['sis_kode']);
            $info[$i] = $data['sis_deskripsi'];
        }
        return $info;
    }

    function sys_() {
        $sql = $this->CI->db->get_where('m_sistem', array('sis_about' => 'sistem'));
        foreach ($sql->result_array() as $data) {
            $sys[$data['sis_kode']] = $data['sis_deskripsi'];
        }
        return $sys;
    }

    function redirect_($level) {
        switch ($level) {
            case 0:
                return base_url('info');
            case 1:
                return base_url('dashboard');
            case 2:
                return base_url('dashboard');
            case 3:
                return base_url('dashboard');
            case 4:
                return base_url('stok');
            case 5:
                return base_url('penjualan');
            default:
                return base_url('logout');
        }
    }

    //number format
    function nf_($number) {
        if (!is_numeric($number)) {
            return $number;
        } else {
            if (strpos($number, '.')) {
                $nf = number_format($number, 3, '.', ',');
            } else {
                $nf = number_format($number, 0, '.', ',');
            }
            return $nf;
        }
    }

    //delimeter convert
    function dc_($number) {
        return str_replace(',', '.', $number);
    }
	
	function cnf_($number){
		return str_replace(',', '', $number);
	}

    function authentication_($user, $pass) {
        $sql = $this->CI->db->get_where($this->sys, array('usr_username' => $user, 'usr_password' => $pass));
        if ($sql->num_rows() == 1) {
            $data['valid'] = TRUE;
            $data['result'] = $sql->row_array();
        } else {
            $data['valid'] = FALSE;
        }
        return $data;
    }

    function newid_($table, $column) {
        $sql = $this->CI->db->select_max($column)->get($table)->row_array();
        return $sql[$column] + 1;
    }

}
