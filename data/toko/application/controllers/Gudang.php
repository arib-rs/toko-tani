<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gudang extends CI_Controller {

    public function index() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(1);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('gudang-menu');
        $header['css'] = array('assets/css/select2/select2.css');
        $header['js'] = array('assets/js/select2.min.js');
        $body = array(
            'tbody' => $this->list_(),
            'optkategori' => $this->optkategori()
        );
        $this->load->view('templates/header', $header);
        $this->load->view('master/gudang', $body);
        $this->load->view('templates/footer');
    }

    function optkategori() {
        $data = $this->Umodel->li_data('m_produk_kategori');
        $opt = '';
        foreach ($data->result_array() as $value) {
            $opt .= '<option value="' . $value['ktg_id'] . '">' . ucfirst($value['ktg_nama']) . '</option>';
        }
        return $opt;
    }

    function list_() {
        $data = $this->Umodel->li_data('m_gudang', 'gdg_nama');
        $no = 1;
        ob_start();
        foreach ($data->result_array() as $vd) {
            ?>
            <tr>
                <td align="center"><?= $no ?></td>
                <td><?= $vd['gdg_nama'] ?></td>
                <td><?= $vd['gdg_alamat'] ?></td>
                <td><?= ($vd['gdg_isjual']) ? 'Kios/Toko' : 'Gudang' ?></td>
                <td><?= $this->convkategori($vd['gdg_produk_kategori']) ?></td>
                <td align="center">
                    <a id="btn-edit" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="left" title="Edit Supplier" name="<?= $vd['gdg_id'] ?>"><i class="fa fa-pencil"></i></a> 
                    <a id="btn-delete" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="left" title="Hapus Supplier" name="<?= $vd['gdg_id'] ?>"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
            <?php
            $no++;
        }
        $tr = ob_get_contents();
        ob_clean();
        return $tr;
    }

    private function convkategori($kategori) {
        if (is_null($kategori)) {
            return $kategori;
        } else {
            $data = $this->Umodel->li_data('m_produk_kategori', 'ktg_nama');
            $arrktg = array();
            foreach ($data->result_array() as $val) {
                $arrktg[$val['ktg_id']] = $val['ktg_nama'];
            }
            $ktg = json_decode($kategori, TRUE);
            $result = array();
            foreach ($ktg as $val) {
                $result[] = $arrktg[$val];
            }
            return implode('<br>', $result);
        }
    }

    function save_() {
        if ($this->input->post('nama') == '') {
            $arr['msg'] = "Data inputan belum lengkap.";
            $arr['ind'] = 0;
            echo json_encode($arr);
            exit();
        }
        $kategori = $this->input->post('kategori');
        $jsonkategori = NULL;
        if (count($kategori) > 0) {
            $jsonkategori = json_encode($kategori);
        }
        $data = array(
            'gdg_nama' => $this->input->post('nama'),
            'gdg_alamat' => $this->input->post('alamat'),
            'gdg_alamat' => $this->input->post('alamat'),
            'gdg_isjual' => $this->input->post('jenis'),
            'gdg_produk_kategori' => $jsonkategori
        );
        if ($this->input->post('id') == '') {
            $data['gdg_id'] = $this->ascfunc->newid_('m_gudang', 'gdg_id');
            $sql = $this->Umodel->sv_data('m_gudang', $data);
            $aksi = 'ditambah';
        } else {
            $sql = $this->Umodel->sv_data('m_gudang', $data, TRUE, array('gdg_id' => $this->input->post('id')));
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
        $data = $this->Umodel->get_data('m_gudang', array('gdg_id' => $id))->row_array();
        $data['produk_kategori'] = json_decode($data['gdg_produk_kategori'], TRUE);
        echo json_encode($data);
    }

    function delete_($id) {
        $sql = $this->Umodel->del_data('m_gudang', array('gdg_id' => $id));
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
