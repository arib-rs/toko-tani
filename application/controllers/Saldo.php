<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Saldo extends CI_Controller {

    public function __construct() {
        parent::__construct();
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
        $header = $this->ascfunc->header_('saldo-menu');
        $header['css'] = array('assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');

        $body = array(
            'cbrek' => $this->cbrek()
        );
        $this->load->view('templates/header', $header);
        $this->load->view('keuangan/saldo', $body);
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

    function filterform_($tahun, $rekening) {
        $q = $this->Umodel->get_data('d_saldo', array('sld_tahun' => $tahun, 'sld_rekening' => $rekening));
        if (count($q->num_rows()) > 0) {
            $dt = $q->row_array();
            $data = array(
                'id' => $dt['sld_id'],
                'debet' => $dt['sld_debet'],
                'kredit' => $dt['sld_kredit']
            );
        } else {
            $data = array(
                'id' => '',
                'debet' => '',
                'kredit' => ''
            );
        }
        ob_start();
        ?>
        <tr>
            <td>
                <input type="hidden" id="id" name="id" class="form-control" value="<?= $data['id'] ?>">
                <input type="text" id="debet" name="debet" class="form-control text-right" value="<?= $data['debet'] ?>" autocomplete="off">
            </td>
            <td>
                <input type="text" id="kredit" name="kredit" class="form-control text-right" value="<?= $data['kredit'] ?>" autocomplete="off">
            </td>
        </tr>
        <?php
        $result['tr'] = ob_get_contents();
        ob_clean();
        echo json_encode($result);
    }

    function filter_() {
        $data = $this->Umodel->li_data('d_saldo', 'sld_tahun desc')->result_array();
        $no = 1;
        ob_start();
        foreach ($data as $val) {
            ?>
            <tr>
                <td class="text-center"><?= $no ?></td>
                <td class="text-center"><?= $val['sld_tahun'] ?></td>
                <td class="text-right"><?= $this->ascfunc->nf_($val['sld_debet']) ?></td>
                <td class="text-right"><?= $this->ascfunc->nf_($val['sld_kredit']) ?></td>
            </tr>
            <?php
            $no++;
        }
        $result['tr'] = ob_get_contents();
        ob_clean();
        echo json_encode($result);
    }

    function save_() {
        $this->load->model('Keuanganmodel');
        $data = array(
            'sld_id' => $this->input->post('id'),
            'sld_rekening' => $this->input->post('rekening'),
            'sld_tahun' => $this->input->post('tahun'),
            'sld_debet' => ($this->input->post('debet') == '') ? NULL : $this->input->post('debet'),
            'sld_kredit' => ($this->input->post('kredit') == '') ? NULL : $this->input->post('kredit')
        );
        $q = $this->Keuanganmodel->sv_saldo($data);
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
