<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Info extends CI_Controller
{

    public function index()
    {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(0);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('info-menu');
        $body = array(
            'content' => $this->content_()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('master/info', $body);
        $this->load->view('templates/footer');
    }

    private function content_()
    {
        $data = $this->ascfunc->info_();
        ob_start();
        ?>
        <div class="row form-group">
            <label class="col-md-2 col-xs-4">Nama Perusahaan</label>
            <div class="col-md-10 col-xs-8">
                <input type="text" id="perusahaan" name="perusahaan" class="form-control" value="<?= $data['perusahaan'] ?>" autocomplete="off">
            </div>
        </div>
        <div class="row form-group">
            <label class="col-md-2 col-xs-4">Deskripsi Perusahaan</label>
            <div class="col-md-10 col-xs-8">
                <input type="text" id="deskripsi" name="deskripsi" class="form-control" value="<?= $data['deskripsi'] ?>" autocomplete="off">
            </div>
        </div>
        <div class="row form-group">
            <label class="col-md-2 col-xs-4">Pemilik Perusahaan</label>
            <div class="col-md-10 col-xs-8">
                <input type="text" id="owner" name="owner" class="form-control" value="<?= $data['owner'] ?>" autocomplete="off">
            </div>
        </div>
        <div class="row form-group">
            <label class="col-md-2 col-xs-2">Kabupaten/Kota</label>
            <div class="col-md-4 col-xs-4">
                <input type="text" id="kabupaten" name="kabupaten" class="form-control" value="<?= $data['kabupaten'] ?>" autocomplete="off">
            </div>
            <label class="col-md-2 col-xs-2">Kecamatan</label>
            <div class="col-md-4 col-xs-4">
                <input type="text" id="kecamatan" name="kecamatan" class="form-control" value="<?= $data['kecamatan'] ?>" autocomplete="off">
            </div>
        </div>
        <div class="row form-group">
            <label class="col-md-2 col-xs-4">Alamat</label>
            <div class="col-md-10 col-xs-8">
                <textarea id="alamat" name="alamat" class="form-control" value="<?= $data['perusahaan'] ?>" autocomplete="off"><?= $data['alamat'] ?></textarea>
            </div>
        </div>
        <div class="row form-group">
            <label class="col-md-2 col-xs-2">No. Telepon</label>
            <div class="col-md-4 col-xs-4">
                <input type="text" id="telepon" name="telepon" class="form-control" value="<?= $data['telepon'] ?>" autocomplete="off">
            </div>
            <label class="col-md-2 col-xs-2">No. HP</label>
            <div class="col-md-4 col-xs-4">
                <input type="text" id="hp" name="hp" class="form-control" value="<?= $data['hp'] ?>" autocomplete="off">
            </div>
        </div>
<?php
        $content = ob_get_contents();
        ob_clean();
        return $content;
    }

    function save_()
    {
        $this->load->model('Mastermodel');
        $data = array(
            'perusahaan' => $this->input->post('perusahaan'),
            'deskripsi' => $this->input->post('deskripsi'),
            'owner' => $this->input->post('owner'),
            'kabupaten' => $this->input->post('kabupaten'),
            'kecamatan' => $this->input->post('kecamatan'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'hp' => $this->input->post('hp')
        );
        $q = $this->Mastermodel->sv_info($data);
        if ($q) {
            $arr['ind'] = 1;
            $arr['msg'] = 'Data berhasil disimpan';
        } else {
            $arr['ind'] = 1;
            $arr['msg'] = 'Data gagal disimpan';
        }
        echo json_encode($arr);
    }
}
