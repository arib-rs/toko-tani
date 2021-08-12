<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Produk extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Mastermodel');
    }

    public function index() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(1);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('produk-menu');
        $body = array(
            'optkategori' => $this->optkategori(),
            'optsupplier' => $this->optsupplier()
        );
        $this->load->view('templates/header', $header);
        $this->load->view('master/produk', $body);
        $this->load->view('templates/footer');
    }

    function optkategori() {
        $data = $this->Umodel->li_data('m_produk_kategori');
        $opt = '<option value="0">-Pilih Kategori Produk-</option>';
        foreach ($data->result_array() as $value) {
            $opt .= '<option value="' . $value['ktg_id'] . '">' . ucfirst($value['ktg_nama']) . '</option>';
        }
        return $opt;
    }
    
    function optsupplier() {
        $data = $this->Umodel->li_data('m_supplier');
        $opt = '<option value="0">-Pilih Supllier-</option>';
        foreach ($data->result_array() as $value) {
            $opt .= '<option value="' . $value['sup_id'] . '">' . ucfirst($value['sup_nama']) . '</option>';
        }
        return $opt;
    }

    function ssplist_($kategori = '0') {
        $aColumns = array('prd_id', 'prd_kode', 'prd_nama', 'prd_satuan', 'sup_nama', 'prd_id');
        $sIndexColumn = $aColumns[0];
        $vColumns = array('prd_nama');

        // paging
        $sLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $sLimit = "LIMIT " . $_GET['iDisplayStart'] . ", " .
                    $_GET['iDisplayLength'];
        }
        $numbering = $_GET['iDisplayStart'];

        // ordering
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY ";

            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " " . $_GET['sSortDir_' . $i] . ", ";
                }
            }
            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = "";
            }
        }

        // filtering
        $sWhere = "";
        if ($_GET['sSearch'] != "") {
            $sWhere = "WHERE (";
            for ($i = 0; $i < count($vColumns); $i++) {
                $sWhere .= $vColumns[$i] . " LIKE '%" . $_GET['sSearch'] . "%' OR ";
            }

            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        }

        // individual column filtering
        for ($i = 0; $i < count($vColumns); $i++) {
            if ($_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != "") {
                if ($sWhere == "") {
                    $sWhere = "WHERE ";
                } else {
                    $sWhere .= " AND ";
                }
                $sWhere .= $vColumns[$i] . " LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
            }
        }
        $rResult = $this->Mastermodel->sspproduk($vColumns, $sWhere, $sOrder, $sLimit, $kategori);
        $iFilteredTotal = 10;
        $rResultTotal = $this->Mastermodel->sspproduk_total($sIndexColumn, $sWhere, $sOrder, $kategori);
        $iTotal = $rResultTotal;

        $iFilteredTotal = $iTotal;
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

        $no = (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') ? $_GET['iDisplayStart'] : 0;
        foreach ($rResult as $aRow) {
            $aksi = '<a id="btn-edit" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="left" title="Edit Produk" data-id="' . $aRow['prd_id'] . '"><i class="fa fa-pencil"></i></a> '
                    . '<a id="btn-delete" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="left" title="Hapus Produk" data-id="' . $aRow['prd_id'] . '"><i class="fa fa-trash"></i></a>';
            $row = array(($no + 1), $aRow['prd_kode'], $aRow['prd_nama'], $aRow['prd_satuan'], $aRow['sup_nama'], $aksi);
            $output['aaData'][] = $row;
            $no++;
        }

        echo json_encode($output);
    }

    function save_() {
        if ($this->input->post('kategori') == '0' || $this->input->post('nama') == '') {
            $arr['msg'] = "Data inputan belum lengkap.";
            $arr['ind'] = 0;
            echo json_encode($arr);
            exit();
        }
        $data = array(
            'prd_id' => $this->input->post('id'),
            'prd_kode' => $this->input->post('kode'),
            'prd_nama' => $this->input->post('nama'),
            'prd_satuan' => $this->input->post('satuan'),
            'prd_ktg_id' => $this->input->post('kategori'),
            'prd_sup_id' => $this->input->post('supplier')
        );
        $sql = $this->Mastermodel->sv_produk($data);
        $aksi = ($this->input->post('id') == '') ? 'ditambah' : 'diedit';

        if ($sql) {
            $arr['kategori'] = $this->input->post('kategori');
            $arr['msg'] = "Data berhasil $aksi.";
            $arr['ind'] = 1;
        } else {
            $arr['msg'] = "Data gagal $aksi.";
            $arr['ind'] = 0;
        }
        echo json_encode($arr);
    }

    function edit_($id) {
        $data = $this->Mastermodel->get_produk($id);
        echo json_encode($data);
    }

    function delete_($id) {
        $sql = $this->Mastermodel->del_produk($id);
        if ($sql) {
            $arr['msg'] = 'Data berhasil dihapus.';
            $arr['ind'] = 1;
        } else {
            $arr['msg'] = 'Terjadi kesalahan, Data gagal dihapus.';
            $arr['ind'] = 0;
        }
        echo json_encode($arr);
    }

}
