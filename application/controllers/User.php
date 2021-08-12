<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
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
        $allowedlevel = array(0);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('user-menu');
        $body = array(
            'tbody' => $this->list_(),
            'opt' => $this->optgudang(),
            'optdaerah' => $this->optdaerah()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('master/user', $body);
        $this->load->view('templates/footer');
    }

    function optgudang()
    {
        $data = $this->Umodel->li_data('m_gudang');
        $opt = '<option value="0">-Pilih Lokasi-</option>';
        foreach ($data->result_array() as $value) {
            $opt .= '<option value="' . $value['gdg_id'] . '" data-jual="' . $value['gdg_isjual'] . '">' . ucfirst($value['gdg_nama']) . '</option>';
        }
        return $opt;
    }
    function optdaerah()
    {
        $data = $this->Umodel->li_data('m_daerah', 'dae_nama');
        $opt = '<option value="0">-Pilih Daerah-</option>';
        foreach ($data->result_array() as $value) {
            $opt .= '<option value="' . $value['dae_id'] . '" >' . ucfirst($value['dae_nama']) . '</option>';
        }
        return $opt;
    }

    function as_($level)
    {
        $as = 'User';
        switch ($level) {
            case 0:
                $as = 'Administrator Sistem';
                break;
            case 1:
                $as = 'Administrator';
                break;
            case 2:
                $as = 'Owner';
                break;
            case 3:
                $as = 'Bagian Keuangan';
                break;
            case 4:
                $as = 'Petugas Gudang';
                break;
            case 5:
                $as = 'Kasir';
                break;
            default:
                break;
        }
        return $as;
    }

    function list_()
    {
        $data = $this->Mastermodel->li_user();
        $no = 1;
        ob_start();
        foreach ($data as $vd) {
            $level = $this->as_($vd['usr_level']);
            $gudang = ($vd['usr_level'] > 3) ? '&nbsp;[' . $vd['gdg_nama'] . ']' : '';
            $lokasi = $this->Umodel->get_data('m_daerah', array('dae_id' => $vd['usr_dae_id']), 'dae_nama')->row_array();
            ?>
                <tr>
                    <td align="center"><?= $no ?></td>
                    <td><?= $vd['usr_nama'] ?></td>
                    <td><?= $vd['usr_username'] ?></td>
                    <td><?= $vd['usr_password'] ?></td>
                    <td><?= $level . $gudang ?></td>
                    <td><?= ($lokasi['dae_nama'] != '' ? $lokasi['dae_nama'] : '-') ?></td>
                    <td align="center">
                        <?php if ($vd['usr_level'] > 0) { ?>
                            <a id="btn-edit" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="left" title="Edit User" name="<?= $vd['usr_id'] ?>"><i class="fa fa-pencil"></i></a>
                            <a id="btn-delete" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="left" title="Hapus User" name="<?= $vd['usr_id'] ?>"><i class="fa fa-trash"></i></a>
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

        function save_()
        {
            if ($this->input->post('nama') == '' || $this->input->post('username') == '' || $this->input->post('password') == '' || $this->input->post('level') == '') {
                $arr['msg'] = "Data inputan belum lengkap.";
                $arr['ind'] = 0;
                echo json_encode($arr);
                exit();
            }
            if ($this->input->post('id') == '') {
                if (!$this->Mastermodel->cek_user($this->input->post('username'))) {
                    $arr['msg'] = "Username telah terdaftar. Silahkan input username lain";
                    $arr['ind'] = 0;
                    echo json_encode($arr);
                    exit();
                }
            } else if ($this->input->post('username') != $this->input->post('usernameold')) {
                if (!$this->Mastermodel->cek_user($this->input->post('username'))) {
                    $arr['msg'] = "Username telah terdaftar. Silahkan input username lain";
                    $arr['ind'] = 0;
                    echo json_encode($arr);
                    exit();
                }
            }
            $data = array(
                'usr_id' => $this->input->post('id'),
                'usr_nama' => $this->input->post('nama'),
                'usr_username' => $this->input->post('username'),
                'usr_password' => $this->input->post('password'),
                'usr_level' => $this->input->post('level'),
                'usr_gdg_id' => ($this->input->post('gudang') == '0') ? NULL : $this->input->post('gudang'),
                'usr_dae_id' => ($this->input->post('daerah') == '0') ? NULL : $this->input->post('daerah')
            );
            $sql = $this->Mastermodel->sv_user($data);
            $aksi = ($this->input->post('id') == '') ? 'ditambah' : 'diedit';

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

        function edit_($id)
        {
            $data = $this->Mastermodel->get_user($id);
            echo json_encode($data);
        }

        function delete_($id)
        {
            $sql = $this->Mastermodel->del_user($id);
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
