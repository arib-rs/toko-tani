<?php

class Login extends CI_Controller
{

    public function index()
    {
        if ($this->session->userdata('log_user')) {
            redirect($this->ascfunc->redirect_($this->session->userdata('log_level')));
        } else {
            $data['info'] = $this->ascfunc->info_();
            $data['version'] = $this->config->item('version');
            $this->load->view('login', $data);
        }
    }

    function auth()
    {
        $username = trim($this->input->post('username'));
        $password = trim($this->input->post('password'));
        if ($username == '' || empty($username) || $password == '' || empty($password)) {
            $arr['ind'] = 0;
            $arr['msg'] = "<font color='red'><i class='fa fa-times'></i> Silahkan isi data secara lengkap.</font>";
        } else {
            $cek = $this->ascfunc->authentication_($username, $password);
            if ($cek['valid']) {
                $sData = array(
                    'log_id' => $cek['result']['usr_id'],
                    'log_nama' => ucfirst($cek['result']['usr_nama']),
                    'log_user' => $cek['result']['usr_username'],
                    'log_level' => $cek['result']['usr_level'],
                    'log_gudang' => $cek['result']['usr_gdg_id'],
                    'log_daerah' => $cek['result']['usr_dae_id']
                );
                $this->session->set_userdata($sData);
                $arr['ind'] = 1;
                $arr['msg'] = "<font color='green'><i class='fa fa-check'></i> Login Berhasil.</font>";
            } else {
                $arr['ind'] = 0;
                $arr['msg'] = "<font color='darkred'><i class='fa fa-times'></i> Login Gagal, Username/Password Salah.</font>";
            }
        }
        echo json_encode($arr);
    }

    function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url('login'));
    }
}
