<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kategori extends CI_Controller {

    public function index() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(1);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('kategori-menu');
        $body = array(
            'tbody' => $this->list_()        
        );
        $this->load->view('templates/header', $header);
        $this->load->view('master/kategori', $body);
        $this->load->view('templates/footer');
    }

    function list_() {
        $data = $this->Umodel->li_data('m_produk_kategori', 'ktg_nama');
        $no = 1;
        ob_start();
        foreach ($data->result_array() as $vd) {
            ?>
            <tr>
                <td align="center"><?= $no ?></td>
                <td><?= $vd['ktg_nama'] ?></td>
                <td align="center">
                    <a id="btn-edit" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="left" title="Edit Supplier" name="<?= $vd['ktg_id'] ?>"><i class="fa fa-pencil"></i></a> 
                    <a id="btn-delete" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="left" title="Hapus Supplier" name="<?= $vd['ktg_id'] ?>"><i class="fa fa-trash"></i></a>
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
            'ktg_nama' => $this->input->post('nama')
        );
        if ($this->input->post('id') == '') {
            $data['ktg_id'] = $this->ascfunc->newid_('m_produk_kategori', 'ktg_id');
            $sql = $this->Umodel->sv_data('m_produk_kategori', $data);
            $aksi = 'ditambah';
        } else {
            $sql = $this->Umodel->sv_data('m_produk_kategori', $data, TRUE, array('ktg_id' => $this->input->post('id')));
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
        $data = $this->Umodel->get_data('m_produk_kategori', array('ktg_id' => $id));
        echo json_encode($data->row_array());
    }

    function delete_($id) {
        $sql = $this->Umodel->del_data('m_produk_kategori', array('ktg_id' => $id));
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
