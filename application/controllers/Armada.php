<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Armada extends CI_Controller {

    public function index() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(1);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('armada-menu');
        $header['css'] = array('assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $body = array(
            'tbody' => $this->list_()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('master/armada', $body);
        $this->load->view('templates/footer');
    }

    function list_() {
        $data = $this->Umodel->li_data('m_armada', 'arm_jenis');
        $no = 1;
        ob_start();
        foreach ($data->result_array() as $vd) {
            ?>
            <tr>
                <td align="center"><?= $no ?></td>
                <td><?= $vd['arm_jenis'] ?></td>
                <td align="center"><?= $vd['arm_nopol'] ?></td>
                <td align="center"><?= ($vd['arm_jtstnk'] == '') ? '' : date('d/m/Y', strtotime($vd['arm_jtstnk'])) ?></td>
                <td align="center"><?= ($vd['arm_jtkir'] == '') ? '' : date('d/m/Y', strtotime($vd['arm_jtkir'])) ?></td>
                <td align="center"><?= ($vd['arm_jther'] == '') ? '' : date('d/m/Y', strtotime($vd['arm_jther'])) ?></td>
                <td align="center">
                    <a id="btn-edit" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="left" title="Edit Armada" name="<?= $vd['arm_id'] ?>"><i class="fa fa-pencil"></i></a> 
                    <a id="btn-delete" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="left" title="Hapus Armada" name="<?= $vd['arm_id'] ?>"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
            <?php
            $no++;
        }
        $tr = ob_get_contents();
        ob_clean();
        return $tr;
    }

    function save_() {
        if ($this->input->post('jenis') == '' || $this->input->post('nopol') == '') {
            $arr['msg'] = "Data inputan belum lengkap.";
            $arr['ind'] = 0;
            echo json_encode($arr);
            exit();
        }
        $data = array(
            'arm_jenis' => $this->input->post('jenis'),
            'arm_nopol' => $this->input->post('nopol'),
            'arm_jtstnk' => ($this->input->post('stnk') == '') ? NULL : date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('stnk')))),
            'arm_jtkir' => ($this->input->post('kir') == '') ? NULL : date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('kir')))),
            'arm_jther' => ($this->input->post('her') == '') ? NULL : date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('her'))))
        );
        if ($this->input->post('id') == '') {
            $data['arm_id'] = $this->ascfunc->newid_('m_armada', 'arm_id');
            $sql = $this->Umodel->sv_data('m_armada', $data);
            $aksi = 'ditambah';
        } else {
            $sql = $this->Umodel->sv_data('m_armada', $data, TRUE, array('arm_id' => $this->input->post('id')));
            $aksi = 'diedit';
        }

        if ($sql) {
            $arr['tbody'] = $this->list_();
            $arr['msg'] = "Data berhasil $aksi.";
            $arr['ind'] = 1;
        } else {
            $arr['msg'] = "Data gagal $aksi.";
            $arr['ind'] = 0;
        }
        echo json_encode($arr);
    }

    function edit_($id) {
        $data = $this->Umodel->get_data('m_armada', array('arm_id' => $id))->row_array();
        $data['arm_jtstnk'] = ($data['arm_jtstnk'] == '') ? '' : date('d/m/Y', strtotime($data['arm_jtstnk']));
        $data['arm_jtkir'] = ($data['arm_jtkir'] == '') ? '' : date('d/m/Y', strtotime($data['arm_jtkir']));
        $data['arm_jther'] = ($data['arm_jther'] == '') ? '' : date('d/m/Y', strtotime($data['arm_jther']));
        echo json_encode($data);
    }

    function delete_($id) {
        $sql = $this->Umodel->del_data('m_armada', array('arm_id' => $id));
        if ($sql) {
            $arr['tbody'] = $this->list_();
            $arr['msg'] = 'Data berhasil dihapus.';
            $arr['ind'] = 1;
        } else {
            $arr['msg'] = 'Terjadi kesalahan, Data gagal dihapus.';
            $arr['ind'] = 0;
        }
        echo json_encode($arr);
    }

}
