<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {

    public function index() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(1, 3, 5);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('customer-menu');
        $body = array(
            'tbody' => $this->list_(),
            'optdaerah' => $this->optdaerah(),
            'optharga' => $this->optharga()
        );
        $this->load->view('templates/header', $header);
        $this->load->view('master/customer', $body);
        $this->load->view('templates/footer');
    }

    function optdaerah() {
        $data = $this->Umodel->li_data('m_daerah', 'dae_nama');
        $opt = '<option value="0">-Pilih Daerah-</option>';
        foreach ($data->result_array() as $value) {
            $opt .= '<option value="' . $value['dae_id'] . '">' . ucfirst($value['dae_nama']) . '</option>';
        }
        return $opt;
    }

    function optharga() {
        $opt = '<option value="0">-Pilih Harga Jual-</option>';
        foreach ($this->ascfunc->harga_ as $key => $value) {
            $opt .= '<option value="' . $key . '">' . $value . '</option>';
        }
        return $opt;
    }

    function list_() {
        $level = $this->session->userdata('log_level');
        $data = $this->Umodel->li_data('m_customer', 'cus_nama');
        $no = 1;
        ob_start();
        foreach ($data->result_array() as $vd) {
            $pemilik = ($vd['cus_iskios']) ? '<br>Pemilik : ' . $vd['cus_pemilik'] : '';
            ?>
            <tr>
                <td align="center"><?= $no ?></td>
                <td><?= $vd['cus_nama'] . $pemilik ?></td>
                <td><?= $vd['cus_telp'] ?></td>
                <td><?= $vd['cus_email'] ?></td>
                <td><?= $vd['cus_bank'] . ' - ' . $vd['cus_rekening'] ?></td>
                <td><?= $vd['cus_alamat'] ?></td>
                <td><?= $this->ascfunc->harga_[$vd['cus_harga']] ?></td>
                <td align="center">
                    <a id="btn-edit" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="left" title="Edit Kecamatan" name="<?= $vd['cus_id'] ?>"><i class="fa fa-pencil"></i></a> 
                    <?php if ($level == 1) { ?>
                        <a id="btn-delete" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="left" title="Hapus Kecamatan" name="<?= $vd['cus_id'] ?>"><i class="fa fa-trash"></i></a>
                        <?php } ?>
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
            'cus_nama' => $this->input->post('nama'),
            'cus_pemilik' => ($this->input->post('jenis')) ? $this->input->post('pemilik') : NULL,
            'cus_telp' => $this->input->post('telp'),
            'cus_email' => $this->input->post('email'),
            'cus_rekening' => $this->input->post('rekening'),
            'cus_bank' => $this->input->post('bank'),
            'cus_alamat' => $this->input->post('alamat'),
            'cus_dae_id' => $this->input->post('daerah'),
            'cus_harga' => $this->input->post('harga'),
            'cus_iskios' => $this->input->post('jenis')
        );
        if ($this->input->post('id') == '') {
            $data['cus_id'] = $this->ascfunc->newid_('m_customer', 'cus_id');
            $sql = $this->Umodel->sv_data('m_customer', $data);
            $aksi = 'ditambah';
        } else {
            $sql = $this->Umodel->sv_data('m_customer', $data, TRUE, array('cus_id' => $this->input->post('id')));
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
        $data = $this->Umodel->get_data('m_customer', array('cus_id' => $id));
        echo json_encode($data->row_array());
    }

    function delete_($id) {
        $sql = $this->Umodel->del_data('m_customer', array('cus_id' => $id));
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
