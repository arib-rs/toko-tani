<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Produkharga extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mastermodel');
    }

    public function index()
    {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(1, 3);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('harga-menu');
        $header['js'] = array('assets/js/bootstrap-typeahead.js');
        $body = array(
            'opt' => $this->optkategori()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('transaksi/produkharga', $body);
        $this->load->view('templates/footer');
    }

    function cari_()
    {
        $search = strtolower($this->input->post('w'));
        $sql = $this->Umodel->find_data('m_produk', array('LOWER(prd_nama)' => $search));
        $dt = array();
        foreach ($sql as $val) {
            $data['id'] = $val['prd_id'];
            $data['hb'] = $val['prd_hargabeli'];
            $data['he'] = $val['prd_hargaecer'];
            $data['hg'] = $val['prd_hargagrosir'];
            $data['hm'] = $val['prd_hargamember'];
            $data['hk'] = $val['prd_hargakhusus'];
            $data['isppn'] = $val['prd_isppn'];
            $kode = ($val['prd_kode'] != '') ? '[' . $val['prd_kode'] . '] ' : '';
            $satuan = ($val['prd_satuan'] != '') ? ' /' . $val['prd_satuan'] : '';
            $data['content'] = $kode . $val['prd_nama'] . $satuan;
            array_push($dt, $data);
        }
        echo json_encode($dt);
    }

    function optkategori()
    {
        $data = $this->Umodel->li_data('m_produk_kategori');
        $opt = '<option value="0">-Semua Kategori Produk-</option>';
        foreach ($data->result_array() as $value) {
            $opt .= '<option value="' . $value['ktg_id'] . '">' . ucfirst($value['ktg_nama']) . '</option>';
        }
        return $opt;
    }

    function ssplist_($kategori = '0')
    {
        $aColumns = array('prd_kode', 'prd_nama', 'prd_hargabeli', 'prd_hargaecer', 'prd_hargagrosir', 'prd_hargamember', 'prd_hargakhusus', 'prd_id');
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
            $ppn = ($aRow['prd_isppn']) ? ' <small class="bg-red pull-right">&nbsp;PPN&nbsp;</small>' : '';
            $kode = ($aRow['prd_kode'] == '') ? '' : '[' . $aRow['prd_kode'] . '] ';
            $satuan = ($aRow['prd_satuan'] == '') ? '' : ' / ' . $aRow['prd_satuan'];
            $span = '';
            $pembagi = $aRow['prd_hargabeli'] * 100;
            $markup = ($pembagi > 0) ? ($aRow['prd_hargaecer'] - $aRow['prd_hargabeli']) / $pembagi : 0;
            if ($markup > 10) {
                $span = '<i class="fa fa-exclamation-circle pull-left text-danger" data-toggle="tooltip" data-placement="left" title="Markup melebihi 10%" style="padding-top: 3px;"></i>';
            }
            $aksi = '<a id="btn-edit" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="left" title="Setting Harga" name="' . $aRow['prd_id'] . '"><i class="fa fa-pencil"></i></a>';
            $row = array(($no + 1), $kode . $aRow['prd_nama'] . $satuan . $ppn, $this->ascfunc->nf_($aRow['prd_hargabeli']), $span . $this->ascfunc->nf_($aRow['prd_hargaecer']), $this->ascfunc->nf_($aRow['prd_hargagrosir']), $this->ascfunc->nf_($aRow['prd_hargamember']), $this->ascfunc->nf_($aRow['prd_hargakhusus']), $aksi);
            $output['aaData'][] = $row;
            $no++;
        }

        echo json_encode($output);
    }

    function save_()
    {
        if ($this->input->post('hb') == '' || $this->input->post('he') == '' || $this->input->post('hg') == '' || $this->input->post('hm') == '' || $this->input->post('hk') == '') {
            $arr['msg'] = "Harga tidak boleh kosong.";
            $arr['ind'] = 0;
            echo json_encode($arr);
            exit();
        }
        $data = array(
            'prd_id' => $this->input->post('id'),
            'prd_hargabeli' => $this->input->post('hb'),
            'prd_hargaecer' => $this->input->post('he'),
            'prd_hargagrosir' => $this->input->post('hg'),
            'prd_hargamember' => $this->input->post('hm'),
            'prd_hargakhusus' => $this->input->post('hk'),
            'prd_isppn' => abs($this->input->post('isppn'))
        );
        $sql = $this->Mastermodel->sv_produk($data);

        if ($sql) {
            $produk = $this->Umodel->get_data('m_produk', array('prd_id' => $this->input->post('id')))->row_array();
            $arr['msg'] = "Harga berhasil diupdate.";
            $arr['kategori'] = $produk['prd_ktg_id'];
            $arr['ind'] = 1;
        } else {
            $arr['msg'] = "Harga gagal diupdate.";
            $arr['ind'] = 0;
        }
        echo json_encode($arr);
    }

    function edit_($id)
    {
        $data = $this->Mastermodel->get_produk($id);
        echo json_encode($data);
    }
}
