<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembayaran extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Keuanganmodel');
    }

    public function index() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(3);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('pembayaran-menu');
        $header['css'] = array('assets/plugin/datetimepicker/css/bootstrap-datetimepicker.min.css', 'assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/js/bootstrap-typeahead.js', 'assets/plugin/moment/moment.min.js', 'assets/plugin/datetimepicker/js/bootstrap-datetimepicker.min.js', 'assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $body = array(
            'optsupplier' => $this->optsupplier(),
            'cbrek' => $this->cbrek()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('keuangan/pembayaransupplier', $body);
        $this->load->view('templates/footer');
    }

    public function piutang() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(3);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('piutang-menu');
        $header['css'] = array('assets/plugin/datetimepicker/css/bootstrap-datetimepicker.min.css', 'assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/js/bootstrap-typeahead.js', 'assets/plugin/moment/moment.min.js', 'assets/plugin/datetimepicker/js/bootstrap-datetimepicker.min.js', 'assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $body = array(
            'optcustomer' => $this->optcustomer(),
            'cbrek' => $this->cbrek()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('keuangan/pembayarancustomer', $body);
        $this->load->view('templates/footer');
    }

    function cbrek() {
        $data = $this->Umodel->li_data('m_rekening');
        $check = TRUE;
        ob_start();
        foreach ($data->result_array() as $value) {
            $checked = ($check) ? 'checked=""' : '';
            ?>
            <label class="radio-inline">
                <input <?= $checked ?> type="radio" name="rekening" id="<?= $value['rek_nomor'] ?>" value="<?= $value['rek_nomor'] ?>"> <?= $value['rek_nomor'] ?>
            </label>
            <?php
            $check = FALSE;
        }
        $cb = ob_get_contents();
        ob_clean();
        return $cb;
    }

    function optsupplier() {
        $data = $this->Umodel->li_data('m_supplier', 'sup_nama');
        $opt = '<option value="0">-Pilih Supplier-</option>';
        foreach ($data->result_array() as $value) {
            $opt .= '<option value="' . $value['sup_id'] . '">' . ucfirst($value['sup_nama']) . '</option>';
        }
        return $opt;
    }

    function optcustomer() {
        $data = $this->Umodel->li_data('m_customer', 'cus_nama');
        $opt = '<option value="0">-Pilih Customer-</option>';
        foreach ($data->result_array() as $value) {
            $pemilik = ($value['cus_iskios']) ? ' - ' . $value['cus_pemilik'] : '';
            $opt .= '<option value="' . $value['cus_id'] . '">' . ucfirst($value['cus_nama']) . $pemilik . '</option>';
        }
        return $opt;
    }

    function filtersupplier_($supplier = '') {
        $total = $this->Keuanganmodel->get_hutangsupplier($supplier, date('Y'));
        $arr['total'] = $total;
        $arr['ftotal'] = 'Rp. ' . $this->ascfunc->nf_($total);
        echo json_encode($arr);
    }

    function filtercustomer_($customer) {
        $arr['div'] = $this->Keuanganmodel->get_piutangcustomer($customer, date('Y'));
        echo json_encode($arr);
    }

    function ssplistpembayaran_($bulan = '', $tahun = '') {
        $aColumns = array('pmb_tanggal', 'sup_nama', 'pmb_nominal', 'pmb_id');
        $sIndexColumn = $aColumns[0];
        $vColumns = array('kio_nama');

        // paging
        $sLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $sLimit = "LIMIT " . $_GET['iDisplayStart'] . ", " .
                    $_GET['iDisplayLength'];
        }
        $numbering = $_GET['iDisplayStart'];

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
        $rResult = $this->Keuanganmodel->sspntpembayaran($vColumns, $sWhere, $sLimit, $bulan, $tahun);
        $iFilteredTotal = 25;
        $rResultTotal = $this->Keuanganmodel->sspntpembayaran_total($sIndexColumn, $sWhere, $bulan, $tahun);
        $iTotal = $rResultTotal;

        $iFilteredTotal = $iTotal;
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

        foreach ($rResult as $aRow) {
            $tanggal = date('d/m/Y', strtotime($aRow['pmb_tanggal'])) . ' ' . date('H:i', strtotime($aRow['pmb_jam']));
            $aksi = '<a id="btn-edit" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="left" title="Edit Nota" data-id="' . $aRow['pmb_id'] . '"><i class="fa fa-pencil"></i></a> <a id="btn-delete" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="left" title="Hapus Nota" data-id="' . $aRow['pmb_id'] . '"><i class="fa fa-trash"></i></a>';
            $row = array($tanggal, $aRow['sup_nama'], 'Rp. ' . $this->ascfunc->nf_($aRow['pmb_nominal']), $aksi);
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    function edit_($id) {
        $data = $this->Keuanganmodel->get_pembayaransupplier($id);
        $arr = $data;
        $arr['tanggal'] = date('d/m/Y H:i', strtotime($data['pmb_tanggal'] . ' ' . $data['pmb_jam']));
        echo json_encode($arr);
    }

    function save_() {
        $tgl = strtotime(str_replace('/', '-', $this->input->post('tanggal')));
        $data = array(
            'pmb_id' => $this->input->post('id'),
            'pmb_tanggal' => date('Y-m-d', $tgl),
            'pmb_jam' => date('H:i:s', $tgl),
            'pmb_nominal' => $this->input->post('jumlah'),
            'pmb_nota_id' => NULL,
            'pmb_sup_id' => $this->input->post('supplier')
        );
        $q = $this->Keuanganmodel->sv_pembayaransupplier($data, $this->input->post('rekening'));
        if ($q) {
            $arr['ind'] = 1;
            $arr['msg'] = 'Data telah tersimpan';
        } else {
            $arr['ind'] = 0;
            $arr['msg'] = 'Terjadi kesalahan, Data gagal disimpan';
        }
        echo json_encode($arr);
    }

    function delete_($id) {
        $q = $this->Keuanganmodel->del_pembayaran($id);
        if ($q) {
            $arr['ind'] = 1;
            $arr['msg'] = 'Data telah dihapus';
        } else {
            $arr['ind'] = 0;
            $arr['msg'] = 'Terjadi kesalahan, Data gagal dihapus';
        }
        echo json_encode($arr);
    }

    function ssplistpiutang_($bulan = '', $tahun = '') {
        $aColumns = array('pmb_tanggal', 'cus_nama', 'pmb_nominal', 'pmb_id');
        $sIndexColumn = $aColumns[0];
        $vColumns = array('kio_nama');

        // paging
        $sLimit = "";
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $sLimit = "LIMIT " . $_GET['iDisplayStart'] . ", " .
                    $_GET['iDisplayLength'];
        }
        $numbering = $_GET['iDisplayStart'];

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
        $rResult = $this->Keuanganmodel->sspntpiutang($vColumns, $sWhere, $sLimit, $bulan, $tahun);
        $iFilteredTotal = 25;
        $rResultTotal = $this->Keuanganmodel->sspntpiutang_total($sIndexColumn, $sWhere, $bulan, $tahun);
        $iTotal = $rResultTotal;

        $iFilteredTotal = $iTotal;
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

        foreach ($rResult as $aRow) {
            $tanggal = date('d/m/Y', strtotime($aRow['pmb_tanggal'])) . ' ' . date('H:i', strtotime($aRow['pmb_jam']));
            $pemilik = ($aRow['cus_iskios']) ? ' - ' . $aRow['cus_pemilik'] : '';
            $aksi = '<a id="btn-delete" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="left" title="Hapus Nota" data-id="' . $aRow['pmb_id'] . '"><i class="fa fa-trash"></i></a>';
            $row = array($tanggal, $aRow['cus_nama'] . $pemilik, 'Rp. ' . $this->ascfunc->nf_($aRow['pmb_nominal']), $aksi);
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    function psave_() {
        $tgl = strtotime(str_replace('/', '-', $this->input->post('tanggal')));
        $data = array(
            'pmb_id' => $this->input->post('id'),
            'pmb_tanggal' => date('Y-m-d', $tgl),
            'pmb_jam' => date('H:i:s', $tgl),
            'pmb_nominal' => $this->input->post('jumlah'),
            'pmb_nota_id' => $this->input->post('idnota'),
            'pmb_sup_id' => NULL
        );
        $q = $this->Keuanganmodel->sv_piutang($data, $this->input->post('rekening'));
        if ($q) {
            $arr['ind'] = 1;
            $arr['msg'] = 'Data telah tersimpan';
        } else {
            $arr['ind'] = 0;
            $arr['msg'] = 'Terjadi kesalahan, Data gagal disimpan';
        }
        echo json_encode($arr);
    }

}
