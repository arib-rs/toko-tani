<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Piutang extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Keuanganmodel');
    }

    public function index() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(1, 2, 3);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('lappiutang-menu');
        $header['css'] = array('assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $body = array(
            'tbody' => $this->list_()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('laporan/piutangcustomer', $body);
        $this->load->view('templates/footer');
    }

    function filter_($tahun) {
        $arr['tbody'] = $this->list_($tahun);
        echo json_encode($arr);
    }

    function list_($tahun = '') {
        $tahun = ($tahun == '') ? date('Y') : $tahun;
        $data = $this->Keuanganmodel->li_piutangcustomer($tahun);
        $no = 1;
		$jml_piutang=0;
        ob_start();
        foreach ($data as $vd) {
            $pemilik = ($vd['cus_iskios']) ? ' - ' . $vd['cus_pemilik'] : '';
            ?>
            <tr>
                <td align="center"><?= $no ?></td>
                <td><?= $vd['cus_nama'] . $pemilik ?></td>
                <td>
                    <?php
                    $total = 0;
                    $li = '<ol style="margin-bottom: 0;padding-left: 18px;">';
                    foreach ($vd['detail'] as $vp) {
                        $li .= '<li>No. Faktur : ' . $vp['id'] . '<br>Tanggal : ' . $vp['tanggal'] . '<br>Piutang : Rp. ' . $this->ascfunc->nf_($vp['piutang']) . '</li>';
                        $total += $vp['piutang'];
                    }
                    $li .= '</ol>';
                    echo $li;
                    ?>
                </td>
                <td align="right"><?= 'Rp. ' . $this->ascfunc->nf_($total) ?></td>
            </tr>
            <?php
			$jml_piutang+=$total;
            $no++;
        }
		?>
			<tr>
                <td align="center"></td>
				<td align="center"></td>
                <td class="text-center"><b>TOTAL</b></td>
                <td align="right"><b><?= 'Rp. ' . $this->ascfunc->nf_($jml_piutang) ?></b></td>
			</tr>
		<?php
        $tr = ob_get_contents();
        ob_clean();
        return $tr;
    }

}
