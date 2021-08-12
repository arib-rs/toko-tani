<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Daerah extends CI_Controller {

    public function index() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(1);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('daerah-menu');
        $body = array(
            'tbody' => $this->list_()
        );
        $this->load->view('templates/header', $header);
        $this->load->view('master/daerah', $body);
        $this->load->view('templates/footer');
    }

    function list_() {
        $data = $this->Umodel->li_data('m_daerah', 'dae_nama');
        $no = 1;
        ob_start();
        foreach ($data->result_array() as $vd) {
            ?>
            <tr>
                <td align="center"><?= $no ?></td>
                <td><?= $vd['dae_nama'] ?></td>
                <td align="center">
                    <a id="btn-edit" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="left" title="Edit Kecamatan" name="<?= $vd['dae_id'] ?>"><i class="fa fa-pencil"></i></a> 
                    <a id="btn-delete" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="left" title="Hapus Kecamatan" name="<?= $vd['dae_id'] ?>"><i class="fa fa-trash"></i></a>
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
        if ($this->input->post('nama') == '') {
            $arr['msg'] = "Data inputan belum lengkap.";
            $arr['ind'] = 0;
            echo json_encode($arr);
            exit();
        }
        $data = array(
            'dae_nama' => $this->input->post('nama')
        );
        if ($this->input->post('id') == '') {
            $data['dae_id'] = $this->ascfunc->newid_('m_daerah', 'dae_id');
            $sql = $this->Umodel->sv_data('m_daerah', $data);
            $aksi = 'ditambah';
        } else {
            $sql = $this->Umodel->sv_data('m_daerah', $data, TRUE, array('dae_id' => $this->input->post('id')));
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
        $data = $this->Umodel->get_data('m_daerah', array('dae_id' => $id));
        echo json_encode($data->row_array());
    }

    function delete_($id) {
        $sql = $this->Umodel->del_data('m_daerah', array('dae_id' => $id));
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
