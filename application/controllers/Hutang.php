<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hutang extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Keuanganmodel');
    }

    public function index()
    {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(1, 3);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('hutang-menu');
        $header['css'] = array('assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $body = array(
            'tbody' => $this->list_()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('laporan/hutangsupplier', $body);
        $this->load->view('templates/footer');
    }

    function filter_($tahun)
    {
        $arr['tbody'] = $this->list_($tahun);
        echo json_encode($arr);
    }

    function list_($tahun = '')
    {


        $tahun = ($tahun == '') ? date('Y') : $tahun;
        $data = $this->Keuanganmodel->li_hutangsupplier($tahun);
        $no = 1;
        $jml_hutang = 0;
        ob_start();
        // var_dump($data[0]['detail'][0]['detail']);
        // die;
        foreach ($data as $vd) {
            ?>
            <tr>
                <td align="center"><?= $no ?></td>
                <td>
                    <?= $vd['sup_nama'] ?>

                </td>
                <td>
                    <?php

                                echo "<ol style='padding-left:18px'>";
                                foreach ($vd['detail'] as $vdd) {
                                    echo "<li style='margin-bottom:20px;'>No Faktur <b>" . $vdd["id"] . "</b> -- " . $vdd["tanggal"] . "<br>";
                                    echo "<ul style='padding-left:18px;margin-top:10px;' >";
                                    $totalpernota = 0;
                                    foreach ($vdd['detail'] as $vddd) {

                                        echo "<li style='margin-bottom:2px'>" . $vddd['dtn_jumlah'] . " " . $vddd['prd_satuan'] . " " . $vddd['prd_nama'] . " -- Rp. " . $this->ascfunc->nf_($vddd['dtn_jumlah'] * $vddd['dtn_hargabeli']) . "</li>";
                                        $totalpernota += $vddd['dtn_jumlah'] * $vddd['dtn_hargabeli'];
                                    }

                                    if ($vdd['ppn'] != 0) {
                                        $ppn = $totalpernota * $vdd['ppn'] / 100;
                                        echo "<li style='list-style-type:none'>Total Harga : Rp. " . $this->ascfunc->nf_($totalpernota) . "</li>";
                                        echo "<li style='list-style-type:none'>PPN (" . $vdd['ppn'] . "%) : Rp. " . $this->ascfunc->nf_($ppn) . "</li>";
                                        $totalpernota += $ppn;
                                    }
                                    echo "<li style='list-style-type:none'>Total : Rp. " . $this->ascfunc->nf_($totalpernota) . "</li>";
                                    echo '</ul>';
                                    echo '</li>';
                                }
                                echo "</ol>"
                                ?>
                </td>
                <td align="right">
                    <?= 'Rp. ' . $this->ascfunc->nf_($vd['hutang']) ?>
                    <!-- <button type="button" class="btn btn-xs btn-primary" title="Lihat Detail" data-toggle="modal" data-target="#ModalDetail<?= $vd['sup_id'] ?>"><i class="fa fa-list"></i></button> -->
                </td>
            </tr>
        <?php

                    $jml_hutang += $vd['hutang'];
                    $no++;
                }
                ?>
        <tr>
            <td align="center"></td>
            <td></td>
            <td align="right"><b>TOTAL</b></td>
            <td align="right"><b><?= 'Rp. ' . $this->ascfunc->nf_($jml_hutang) ?></b></td>
        </tr>
<?php
        $tr = ob_get_contents();
        ob_clean();
        return $tr;
    }
}
