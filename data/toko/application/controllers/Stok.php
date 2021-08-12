<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Stok extends CI_Controller {

    public function index() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(1, 2, 4);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('stok-menu');
        $header['css'] = array('assets/plugin/datetimepicker/css/bootstrap-datetimepicker.min.css', 'assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/js/bootstrap-typeahead.js', 'assets/plugin/moment/moment.min.js', 'assets/plugin/datetimepicker/js/bootstrap-datetimepicker.min.js', 'assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $sys = $this->ascfunc->sys_();
        $header['ppn'] = $sys['ppn'];
        $gudang = ($this->session->userdata('log_level') == 4) ? $this->session->userdata('log_gudang') : '';
        $body = array(
            'level' => $this->session->userdata('log_level'),
            'optgudang' => $this->optgudang($gudang)
        );
        $this->load->view('templates/header', $header);
        $this->load->view('logistik/stok', $body);
        $this->load->view('templates/footer');
    }

    function optgudang($selected = '') {
        $data = $this->Umodel->li_data('m_gudang', 'gdg_nama');
        $opt = '<option value="0">-Pilih Lokasi-</option>';
        foreach ($data->result_array() as $value) {
            $sel = ($selected == $value['gdg_id']) ? 'selected=""' : '';
            $opt .= '<option ' . $sel . ' value="' . $value['gdg_id'] . '">' . $value['gdg_nama'] . '</option>';
        }
        return $opt;
    }

    function ssplist_($gudang = '0') {
        $this->load->model('Mastermodel');
        $aColumns = array('prd_id', 'prd_kode', 'prd_nama', 'stok', 'prd_satuan');
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
        $rResult = $this->Mastermodel->sspstok($vColumns, $sWhere, $sOrder, $sLimit, $gudang);
        $iFilteredTotal = 10;
        $rResultTotal = $this->Mastermodel->sspstok_total($sIndexColumn, $sWhere, $sOrder, $gudang);
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
            $history_ = '<a class="pull-right" href="' . base_url('stok/detail') . '/' . $aRow['prd_id'] . '/' . $gudang . '" data-toggle="tooltip" data-placement="left" title="History Barang"><i class="fa fa-history"></i></a>';
            $row = array(($no + 1), $aRow['prd_kode'], $aRow['prd_nama'] . $history_, $this->ascfunc->nf_($aRow['stok']), $aRow['prd_satuan']);
            $output['aaData'][] = $row;
            $no++;
        }

        echo json_encode($output);
    }

    function detail($id, $gudang) {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $alloweduser = array(1, 2, 4);
        if (!in_array($this->session->userdata('log_level'), $alloweduser)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('stok-menu');
        $produk = $this->Umodel->get_data('m_produk', array('prd_id' => $id))->row_array();
        $body = array(
            'item_id' => $id,
            'item_nama' => $produk['prd_nama'],
            'gudang_id' => $gudang,
        );
        $this->load->view('templates/header', $header);
        $this->load->view('logistik/stok_detail', $body);
        $this->load->view('templates/footer');
    }

    function ssplistdetail_($id, $gudang) {
        $this->load->model('Mastermodel');
        $aColumns = array('stk_tanggal', 'nota_cb', 'nota_pj', 'stk_tanggal', 'stk_jam', 'stk_jumlah');
        $sIndexColumn = $aColumns[0];
        $vColumns = array('nota_keterangan');

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
        $rResult = $this->Mastermodel->sspstokdetail($vColumns, $sWhere, $sOrder, $sLimit, $id, $gudang);
        $iFilteredTotal = 10;
        $rResultTotal = $this->Mastermodel->sspstokdetail_total($sIndexColumn, $sWhere, $sOrder, $id, $gudang);
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
            $row = array(($no + 1), $this->keterangan($aRow['nota_jenis'], $aRow['nota_asal'], $aRow['nota_tujuan']), '<i class="fa fa-stack-overflow"></i> ' . $aRow['nota_cb'], date('d/m/Y', strtotime($aRow['stk_tanggal'])), date('H:i', strtotime($aRow['stk_jam'])), $this->ascfunc->nf_($aRow['stk_jumlah']));
            $output['aaData'][] = $row;
            $no++;
        }

        echo json_encode($output);
    }
    
    private function keterangan($jenis, $asal, $tujuan) {
        switch ($jenis) {
            case 'BELI':
                $asl = $this->Umodel->get_data('m_supplier', array('sup_id' => $asal))->row_array();
                $ket = 'Barang Masuk dari '.$asl['sup_nama'];
                break;
            case 'PINDAH':
                $asl = $this->Umodel->get_data('m_gudang', array('gdg_id' => $asal))->row_array();
                $tj = $this->Umodel->get_data('m_gudang', array('gdg_id' => $tujuan))->row_array();
                $ket = 'Barang Keluar dari '.$asl['gdg_nama'].' ke '.$tj['gdg_nama'];
                break;
            case 'JUAL':
                $tj = $this->Umodel->get_data('m_customer', array('cus_id' => $tujuan))->row_array();
                $ket = 'Barang Terjual ke Customer '.$tj['cus_nama'];
                break;
            case 'RETUR':                
                $asl = $this->Umodel->get_data('m_gudang', array('gdg_id' => $asal))->row_array();
                $tj = $this->Umodel->get_data('m_supplier', array('sup_id' => $tujuan))->row_array();
                $ket = 'Barang Retur dari '.$asl['gdg_nama'].' ke Supplier '.$tj['sup_nama'];
                break;
            case 'RETURK':                
                $asl = $this->Umodel->get_data('m_supplier', array('sup_id' => $asal))->row_array();
                $tj = $this->Umodel->get_data('m_gudang', array('gdg_id' => $tujuan))->row_array();
                $ket = 'Barang Retur Kembali dari Supplier '.$asl['sup_nama'].' ke '.$tj['gdg_nama'];
                break;
            default:
                $ket = '';
                break;
        }
        return $ket;
    }

}
