<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller {

    public function index() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        } else {
            $this->session->sess_destroy();
            redirect(base_url('login'));
        }
    }

}