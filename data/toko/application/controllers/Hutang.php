<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hutang extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Keuanganmodel');
    }

    public function index() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(1, 3);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('hutang-menu');
        $header['css'] = array('assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $body = array(
            'tbody' => $this->list_()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('laporan/hutangsupplier', $body);
        $this->load->view('templates/footer');
    }

    function filter_($tahun) {
        $arr['tbody'] = $this->list_($tahun);
        echo json_encode($arr);
    }

    function list_($tahun = '') {
        $tahun = ($tahun == '') ? date('Y') : $tahun;
        $data = $this->Keuanganmodel->li_hutangsupplier($tahun);
        $no = 1;
        ob_start();
        foreach ($data as $vd) {
            ?>
            <tr>
                <td align="center"><?= $no ?></td>
                <td><?= $vd['sup_nama'] ?></td>
                <td align="right"><?= 'Rp. ' . $this->ascfunc->nf_($vd['hutang']) ?></td>
            </tr>
            <?php
            $no++;
        }
        $tr = ob_get_contents();
        ob_clean();
        return $tr;
    }

}
