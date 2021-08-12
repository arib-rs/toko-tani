<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laporan extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Laporanmodel');
    }

    public function index()
    {
        show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
        exit();
    }

    public function opname()
    {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(1, 2, 3);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('lapopname-menu');
        $header['css'] = array('assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $body = array(
            'optgudang' => $this->optgudang()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('laporan/opname', $body);
        $this->load->view('templates/footer');
    }

    function optgudang($selected = '')
    {
        $data = $this->Umodel->li_data('m_gudang', 'gdg_nama');
        $opt = '<option value="0">-Pilih Lokasi-</option>';
        foreach ($data->result_array() as $value) {
            $sel = ($selected == $value['gdg_id']) ? 'selected=""' : '';
            $opt .= '<option ' . $sel . ' value="' . $value['gdg_id'] . '">' . $value['gdg_nama'] . '</option>';
        }
        return $opt;
    }

    private function opnamecontent_($gudang, $tanggal)
    {
        $data = $this->Laporanmodel->li_opname($gudang, $tanggal);
        ob_start();
        $total = 0;
        $no = 1;
        if (count($data) > 0) {
            foreach ($data as $val) {
                $jumlah = $val['stok'] * $val['prd_hargabeli'];
                ?>
                <tr>
                    <td class="text-center"><?= $no ?></td>
                    <td class="text-center"><?= $val['prd_kode'] ?></td>
                    <td class="text-center"><?= $val['stk_nobatch'] ?></td>
                    <td><?= $val['prd_nama'] ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['stok']) ?></td>
                    <td class="text-center"><?= date('d/m/Y', strtotime($val['stk_kadaluarsa'])) ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['prd_hargabeli']) ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($jumlah) ?></td>
                    <td><?= $val['sup_nama'] ?></td>
                </tr>
            <?php
                            $total += $jumlah;
                            $no++;
                        }
                    } else {
                        ?>
            <tr>
                <td class="text-center" colspan="9">Tidak ada data...</td>
            </tr>
        <?php
                }
                $result['tbody'] = ob_get_contents();
                ob_clean();
                ?>
        <tr style="font-weight: bold">
            <td class="text-center" colspan="7">Total</td>
            <td class="text-right"><?= $this->ascfunc->nf_($total) ?></td>
            <td></td>
        </tr>
        <?php
                $result['tfoot'] = ob_get_contents();
                ob_clean();
                return $result;
            }

            function opnamefilter_()
            {
                $tgl = $this->input->post('tanggal');
                $gudang = $this->input->post('gudang');
                $tanggal = ($tgl == '') ? date('Y-m-d') : date('Y-m-d', strtotime(str_replace('/', '-', $tgl)));
                $arr = $this->opnamecontent_($gudang, $tanggal);
                echo json_encode($arr);
            }

            function opnamepdf_()
            {
                if (!$this->session->userdata('log_user')) {
                    redirect(base_url('login'));
                }
                $tgl = urldecode($this->input->get('tanggal'));
                $gudang = $this->input->get('gudang');
                $tanggal = ($tgl == '') ? date('Y-m-d') : date('Y-m-d', strtotime(str_replace('/', '-', $tgl)));
                $data = $this->opnamecontent_($gudang, $tanggal);
                $tgl_ = strtotime($tanggal);
                $data['info'] = $this->ascfunc->info_();
                $data['tanggal'] = date('d', $tgl_) . ' ' . $this->ascfunc->bulan_[date('m', $tgl_)] . ' ' . date('Y', $tgl_);
                $gdg = $this->Umodel->get_data('m_gudang', array('gdg_id' => $gudang))->row_array();
                $data['gudang'] = $gdg['gdg_nama'];
                $this->load->helper(array('dompdf', 'file'));
                $html = $this->load->view('pdf/opname', $data, true);
                pdf_create($html, 'LAPORAN_OPNAME_' . date('d-m-Y', $tgl_), "potrait");
            }

            public function operasional()
            {
                if (!$this->session->userdata('log_user')) {
                    redirect(base_url('login'));
                }
                $allowedlevel = array(1, 2, 3);
                if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
                    show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
                    exit();
                }
                $header = $this->ascfunc->header_('lapoperasional-menu');
                $header['css'] = array('assets/plugin/datepicker/bootstrap-datepicker.css', 'assets/plugin/daterangepicker/daterangepicker-bs3.css');
                $header['js'] = array('assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js', 'assets/plugin/moment/moment.js', 'assets/plugin/daterangepicker/daterangepicker.js');
                $body = array();
                $this->load->view('templates/header', $header);
                $this->load->view('laporan/operasional', $body);
                $this->load->view('templates/footer');
            }

            private function operasionalcontent_($tstart, $tend)
            {
                $data = $this->Laporanmodel->li_operasional($tstart, $tend);
                ob_start();
                $total = 0;
                $no = 1;
                if (count($data) > 0) {
                    foreach ($data as $val) {
                        $tanggal = date('d/m/Y', strtotime($val['kas_tanggal'])) . ' ' . date('H:i', strtotime($val['kas_jam']));
                        $jumlah = $val['kas_kredit'];
                        $keterangan = ($val['kas_keterangan'] == '') ? '' : '<br>' . $val['kas_keterangan'];
                        ?>
                <tr>
                    <td class="text-center"><?= $no ?></td>
                    <td class="text-center"><?= $tanggal ?></td>
                    <td><?= $val['kas_uraian'] . $keterangan ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['kas_kredit']) ?></td>
                </tr>
            <?php
                            $total += $jumlah;
                            $no++;
                        }
                    } else {
                        ?>
            <tr>
                <td class="text-center" colspan="7">Tidak ada data...</td>
            </tr>
        <?php
                }
                $result['tbody'] = ob_get_contents();
                ob_clean();
                ?>
        <tr style="font-weight: bold">
            <td class="text-center" colspan="3">Total</td>
            <td class="text-right"><?= $this->ascfunc->nf_($total) ?></td>
        </tr>
        <?php
                $result['tfoot'] = ob_get_contents();
                ob_clean();
                return $result;
            }

            function operasionalfilter_()
            {
                if ($this->input->post('tanggal') == '') {
                    $ts = $te = date('Y-m-d');
                } else {
                    $tgl = explode(' - ', $this->input->post('tanggal'));
                    $ts = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[0])));
                    $te = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[1])));
                }
                $arr = $this->operasionalcontent_($ts, $te);
                echo json_encode($arr);
            }

            function operasionalpdf_()
            {
                if (!$this->session->userdata('log_user')) {
                    redirect(base_url('login'));
                }
                if ($this->input->get('tanggal') == '') {
                    $ts = $te = date('Y-m-d');
                } else {
                    $tgl = explode(' - ', urldecode($this->input->get('tanggal')));
                    $ts = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[0])));
                    $te = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[1])));
                }
                $data = $this->operasionalcontent_($ts, $te);
                $data['info'] = $this->ascfunc->info_();
                $data['tanggal'] = date('d-m-Y', strtotime($ts)) . ' s/d ' . date('d-m-Y', strtotime($te));
                $this->load->helper(array('dompdf', 'file'));
                $html = $this->load->view('pdf/operasional', $data, true);
                pdf_create($html, 'LAPORAN_OERASIONAL_[' . date('d-m-Y', strtotime($ts)) . ' s/d ' . date('d-m-Y', strtotime($te)) . ']', "potrait");
            }

            public function adminsales()
            {
                if (!$this->session->userdata('log_user')) {
                    redirect(base_url('login'));
                }
                $allowedlevel = array(1, 2, 3);
                if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
                    show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
                    exit();
                }
                $header = $this->ascfunc->header_('lapadminsales-menu');
                $header['css'] = array('assets/plugin/datepicker/bootstrap-datepicker.css', 'assets/plugin/daterangepicker/daterangepicker-bs3.css');
                $header['js'] = array('assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js', 'assets/plugin/moment/moment.js', 'assets/plugin/daterangepicker/daterangepicker.js');
                $body = array(
                    'optadmin' => $this->optadmin()
                );
                $this->load->view('templates/header', $header);
                $this->load->view('laporan/adminsales', $body);
                $this->load->view('templates/footer');
            }
            public function penjualan()
            {
                if (!$this->session->userdata('log_user')) {
                    redirect(base_url('login'));
                }
                $allowedlevel = array(1, 2, 3);
                if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
                    show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
                    exit();
                }
                $header = $this->ascfunc->header_('lappenjualan-menu');
                $header['css'] = array('assets/plugin/datepicker/bootstrap-datepicker.css', 'assets/plugin/daterangepicker/daterangepicker-bs3.css');
                $header['js'] = array('assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js', 'assets/plugin/moment/moment.js', 'assets/plugin/daterangepicker/daterangepicker.js');
                $body = array(
                    'opttoko' => $this->opttoko()
                );
                $this->load->view('templates/header', $header);
                $this->load->view('laporan/penjualan', $body);
                $this->load->view('templates/footer');
            }
            public function pembelian()
            {
                if (!$this->session->userdata('log_user')) {
                    redirect(base_url('login'));
                }
                $allowedlevel = array(1, 2, 3);
                if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
                    show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
                    exit();
                }
                $header = $this->ascfunc->header_('lappembelian-menu');
                $header['css'] = array('assets/plugin/datepicker/bootstrap-datepicker.css', 'assets/plugin/daterangepicker/daterangepicker-bs3.css');
                $header['js'] = array('assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js', 'assets/plugin/moment/moment.js', 'assets/plugin/daterangepicker/daterangepicker.js');
                $body = array(
                    'optsupplier' => $this->optsupplier()
                );
                $this->load->view('templates/header', $header);
                $this->load->view('laporan/pembelian', $body);
                $this->load->view('templates/footer');
            }

            function optadmin($selected = '')
            {
                $data = $this->Umodel->get_data('zx_xvrty', array('usr_level' => 5), 'usr_nama');
                $opt = '';
                foreach ($data->result_array() as $value) {
                    $sel = ($selected == $value['usr_nama']) ? 'selected=""' : '';
                    $opt .= '<option ' . $sel . ' value="' . $value['usr_nama'] . '">' . $value['usr_nama'] . '</option>';
                }
                return $opt;
            }
            function opttoko($selected = '')
            {
                $data = $this->Umodel->get_data('m_gudang', array('gdg_isjual' => 1), 'gdg_nama');
                $opt = '';
                foreach ($data->result_array() as $value) {
                    $sel = ($selected == $value['gdg_id']) ? 'selected=""' : '';
                    $opt .= '<option ' . $sel . ' value="' . $value['gdg_id'] . '">' . $value['gdg_nama'] . '</option>';
                }
                return $opt;
            }
            function optsupplier($selected = '')
            {
                $data = $this->Umodel->li_data('m_supplier', 'sup_nama');
                $opt = '';
                foreach ($data->result_array() as $value) {
                    $sel = ($selected == $value['sup_id']) ? 'selected=""' : '';
                    $opt .= '<option ' . $sel . ' value="' . $value['sup_id'] . '">' . $value['sup_nama'] . '</option>';
                }
                return $opt;
            }

            private function adminsalescontent_($admin, $tstart, $tend)
            {
                $data = $this->Laporanmodel->li_penjualan_admin($admin, $tstart, $tend);
                ob_start();
                $thj =  0;
                $tkredit = $tlunas = 0;
                $no = 1;

                if (count($data) > 0) {
                    foreach ($data as $val) {
                        ?>
                <tr>
                    <td class="text-center"><?= $no ?></td>
                    <td class="text-center"><?= $val['tanggal'] ?></td>
                    <td><?= $val['customer'] ?></td>
                    <td><?= $val['nonota'] ?></td>
                    <td><?= $val['produk'] ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['jumlah']) ?></td>

                    <td class="text-right"><?= $this->ascfunc->nf_($val['hargajual']) ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['jumlah'] * $val['hargajual']) ?></td>

                    <?php
                                    if ($val['kredit'] == 1) {
                                        ?>
                        <td class="text-center" style="background:tomato">TIDAK LUNAS</td>
                    <?php
                                    } else {
                                        ?>
                        <td class="text-center" style="background:lime">LUNAS</td>
                    <?php
                                    }

                                    ?>


                </tr>
            <?php

                            $thj += $val['jumlah'] * $val['hargajual'];
                            if ($val['kredit'] == 1) {
                                $tkredit += $val['jumlah'] * $val['hargajual'];
                            } else {
                                $tlunas += $val['jumlah'] * $val['hargajual'];
                            }
                            $no++;
                        }
                    } else {
                        ?>
            <tr>
                <td class="text-center" colspan="10">Tidak ada data...</td>
            </tr>
        <?php
                }
                $result['tbody'] = ob_get_contents();
                ob_clean();
                ?>

        <tr style="font-weight: bold">
            <td colspan="7">Total Lunas</td>
            <td colspan="2" class="text-right"><?= $this->ascfunc->nf_($tlunas) ?></td>
        </tr>
        <tr style="font-weight: bold">
            <td colspan="7">Total Kredit</td>
            <td colspan="2" class="text-right"><?= $this->ascfunc->nf_($tkredit) ?></td>
        </tr>
        <tr style="font-weight: bold">
            <td colspan="7">Total Keseluruhan</td>
            <td colspan="2" class="text-right"><?= $this->ascfunc->nf_($thj) ?></td>
        </tr>
        <?php
                $result['tfoot'] = ob_get_contents();
                ob_clean();
                return $result;
            }
            private function penjualancontent_($toko, $tstart, $tend)
            {
                $data = $this->Laporanmodel->li_penjualan($toko, $tstart, $tend);
                ob_start();
                $thb = $thj = $tprf = 0;
                $thbpn = $thjpn = $tprfpn = 0;
                $notabefore = '';
                $customer = '';
                $cus_id = '';
                $sumnota = array();

                if (count($data) > 0) {
                    foreach ($data as $val) {
                        if ($notabefore != '') {
                            if ($val['nota'] != '') {
                                ?>
                        <tr style="font-weight: bold">
                            <td class="text-center" colspan="5">Total</td>
                            <td class="text-right"><?= $this->ascfunc->nf_($thbpn) ?></td>
                            <td></td>
                            <td class="text-right"><?= $this->ascfunc->nf_($thjpn) ?></td>
                            <td></td>
                            <td></td>
                            <td class="text-right"><?= $this->ascfunc->nf_($tprfpn) ?></td>
                        </tr>
                <?php
                                        echo "<tr><td style='background:#fafaf8' colspan='11'></td></tr>";

                                        $sumnota[] = array(
                                            'cus_id' => $cus_id,
                                            'customer' => $customer,
                                            'thbpn' => $thbpn,
                                            'thjpn' => $thjpn,
                                            'tprfpn' => $tprfpn
                                        );
                                        $notabefore = $val['nota'];
                                        $customer = $val['customer'];
                                        $cus_id = $val['cus_id'];
                                        $thbpn = $thjpn = $tprfpn = 0;
                                    }
                                } else {
                                    $notabefore = $val['nota'];
                                    $customer = $val['customer'];
                                    $cus_id = $val['cus_id'];
                                    $thbpn = $thjpn = $tprfpn = 0;
                                }
                                ?>
                <tr>
                    <td class="text-center"><?= $val['tanggal'] ?></td>
                    <td><?= $val['customer'] ?></td>
                    <td><?= $val['produk'] ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['jumlah']) ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['hargabeli']) ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['jumlah'] * $val['hargabeli']) ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['hargajual']) ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['jumlah'] * $val['hargajual']) ?></td>
                    <td class="text-right"><?= ($val['kredit'] == 1) ? 'TIDAK LUNAS' : 'LUNAS'  ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['hargajual'] - $val['hargabeli']) ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['jumlah'] * ($val['hargajual'] - $val['hargabeli'])) ?></td>
                </tr>
            <?php
                            $thb += $val['jumlah'] * $val['hargabeli'];
                            $thbpn += $val['jumlah'] * $val['hargabeli'];
                            $thj += $val['jumlah'] * $val['hargajual'];
                            $thjpn += $val['jumlah'] * $val['hargajual'];
                            $tprf += $val['jumlah'] * ($val['hargajual'] - $val['hargabeli']);
                            $tprfpn += $val['jumlah'] * ($val['hargajual'] - $val['hargabeli']);
                        }
                        $sumnota[] = array(
                            'cus_id' => $val['cus_id'],
                            'customer' => $customer,
                            'thbpn' => $thbpn,
                            'thjpn' => $thjpn,
                            'tprfpn' => $tprfpn
                        );
                        ?>
            <tr style="font-weight: bold">
                <td class="text-center" colspan="5">Total</td>
                <td class="text-right"><?= $this->ascfunc->nf_($thbpn) ?></td>
                <td></td>
                <td class="text-right"><?= $this->ascfunc->nf_($thjpn) ?></td>
                <td></td>
                <td></td>
                <td class="text-right"><?= $this->ascfunc->nf_($tprfpn) ?></td>
            </tr>

        <?php
                } else {
                    ?>
            <tr>
                <td class="text-center" colspan="10">Tidak ada data...</td>
            </tr>
        <?php
                }
                $result['tbody'] = ob_get_contents();
                ob_clean();
                ?>
        <tr>
            <td style='background:#fafaf8' colspan='11'></td>
        </tr>
        <?php
                $sumcustomer = array();
                foreach ($sumnota as $sn) {
                    if (isset($sumcustomer[$sn['cus_id']])) {
                        $sumcustomer[$sn['cus_id']]['thb'] += $sn['thbpn'];
                        $sumcustomer[$sn['cus_id']]['thj'] += $sn['thjpn'];
                        $sumcustomer[$sn['cus_id']]['tprf'] += $sn['tprfpn'];
                    } else {
                        $sumcustomer[$sn['cus_id']]['nama'] = $sn['customer'];
                        $sumcustomer[$sn['cus_id']]['thb'] = $sn['thbpn'];
                        $sumcustomer[$sn['cus_id']]['thj'] = $sn['thjpn'];
                        $sumcustomer[$sn['cus_id']]['tprf'] = $sn['tprfpn'];
                    }
                    ?>
            <!-- <tr>
                <td colspan="5"><?= $sn['cus_id'] . ' ' . $sn['customer'] ?></td>
                <td class="text-right"><?= $this->ascfunc->nf_($sn['thbpn']) ?></td>
                <td></td>
                <td class="text-right"><?= $this->ascfunc->nf_($sn['thjpn']) ?></td>
                <td></td>
                <td></td>
                <td class="text-right"><?= $this->ascfunc->nf_($sn['tprfpn']) ?></td>
            </tr> -->
        <?php

                }
                echo "<tr><td class='text-center' colspan='11'><b>Summary</b></td></tr>";
                foreach ($sumcustomer as $sc) {
                    ?>
            <tr>
                <td colspan="5"><?= $sc['nama'] ?></td>
                <td class="text-right"><?= $this->ascfunc->nf_($sc['thb']) ?></td>
                <td></td>
                <td class="text-right"><?= $this->ascfunc->nf_($sc['thj']) ?></td>
                <td></td>
                <td></td>
                <td class="text-right"><?= $this->ascfunc->nf_($sc['tprf']) ?></td>
            </tr>
        <?php
                }
                ?>
        <tr style="font-weight: bold">
            <td class="text-center" colspan="5">Grand Total</td>
            <td class="text-right"><?= $this->ascfunc->nf_($thb) ?></td>
            <td></td>
            <td class="text-right"><?= $this->ascfunc->nf_($thj) ?></td>
            <td></td>
            <td></td>
            <td class="text-right"><?= $this->ascfunc->nf_($tprf) ?></td>
        </tr>
        <?php
                $result['tfoot'] = ob_get_contents();
                ob_clean();
                return $result;
            }
            private function pembeliancontent_($supplier, $tstart, $tend)
            {
                $data = $this->Laporanmodel->li_pembelian($supplier, $tstart, $tend);
                ob_start();
                $thb = $thj = $tprf = 0;
                $thb = $tpn = 0;
                $notabefore = '';
                if (count($data) > 0) {
                    foreach ($data as $val) {
                        $totalharga = ($val['jumlah'] * $val['hargabeli']) + (($val['jumlah'] * $val['hargabeli']) * $val['ppn'] / 100);

                        if ($notabefore != '') {
                            if ($val['nota'] != '') {
                                echo "<tr><td class='text-right' colspan='7'>Total</td><td class='text-right'><b>Rp. " . $this->ascfunc->nf_($tpn) . "</b></td></tr>";
                                echo "<tr><td style='background:#fafaf8' colspan='8'></td></tr>";
                                $notabefore = $val['nota'];
                                $tpn = 0;
                            }
                        } else {
                            $notabefore = $val['nota'];
                            $tpn = 0;
                        }

                        ?>
                <tr>
                    <td class="text-center"><?= $val['tanggal'] ?></td>
                    <td><?= $val['supplier'] ?></td>
                    <td><?= $val['nota'] ?></td>
                    <td class="text-center"><?= $this->ascfunc->nf_($val['jumlah']) . ' ' . $val['satuan'] ?></td>
                    <td><?= $val['produk'] ?></td>
                    <td class="text-right">Rp. <?= $this->ascfunc->nf_($val['hargabeli']) ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['ppn']) ?> %</td>
                    <td class="text-right">Rp. <?= $this->ascfunc->nf_($totalharga) ?></td>
                    <!-- <td class="text-center"><?= ($val['kredit'] == 1) ? 'KREDIT' : 'LUNAS' ?></td> -->

                </tr>
            <?php


                            $tpn += $totalharga;
                            $thb += $totalharga;
                            // $thb += $val['jumlah'] * $val['hargabeli'];
                            // $thj += $val['jumlah'] * $val['hargajual'];
                            // $tprf += $val['jumlah'] * ($val['hargajual'] - $val['hargabeli']);
                        }
                        echo "<tr><td class='text-right' colspan='7'>Total</td><td class='text-right'><b>Rp. " . $this->ascfunc->nf_($tpn) . "</b></td></tr>";
                    } else {
                        ?>
            <tr>
                <td class="text-center" colspan="8">Tidak ada data...</td>
            </tr>
        <?php
                }
                $result['tbody'] = ob_get_contents();
                ob_clean();
                ?>
        <tr>
            <td style='background:#fafaf8' colspan='8'></td>
        </tr>
        <tr style="font-weight: bold">
            <td class="text-right" colspan="7">Grand Total</td>
            <td class="text-right">Rp. <?= $this->ascfunc->nf_($thb) ?></td>
            <!-- <td></td> -->
            <!-- <td class="text-right"><?= $this->ascfunc->nf_($thj) ?></td>
            <td></td>
            <td class="text-right"><?= $this->ascfunc->nf_($tprf) ?></td> -->
        </tr>
        <?php
                $result['tfoot'] = ob_get_contents();
                ob_clean();
                return $result;
            }

            function adminsalesfilter_()
            {
                if ($this->input->post('tanggal') == '') {
                    $ts = $te = date('Y-m-d');
                } else {
                    $tgl = explode(' - ', $this->input->post('tanggal'));
                    $ts = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[0])));
                    $te = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[1])));
                }
                $admin = $this->input->post('admin');
                $arr = $this->adminsalescontent_($admin, $ts, $te);
                echo json_encode($arr);
            }
            function penjualanfilter_()
            {
                if ($this->input->post('tanggal') == '') {
                    $ts = $te = date('Y-m-d');
                } else {
                    $tgl = explode(' - ', $this->input->post('tanggal'));
                    $ts = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[0])));
                    $te = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[1])));
                }
                $toko = $this->input->post('toko');
                $arr = $this->penjualancontent_($toko, $ts, $te);
                echo json_encode($arr);
            }
            function pembelianfilter_()
            {
                if ($this->input->post('tanggal') == '') {
                    $ts = $te = date('Y-m-d');
                } else {
                    $tgl = explode(' - ', $this->input->post('tanggal'));
                    $ts = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[0])));
                    $te = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[1])));
                }
                $supplier = $this->input->post('supplier');
                $arr = $this->pembeliancontent_($supplier, $ts, $te);
                echo json_encode($arr);
            }

            function adminsalesexcel_()
            {
                if (!$this->session->userdata('log_user')) {
                    redirect(base_url('login'));
                }
                if ($this->input->get('tanggal') == '') {
                    $ts = $te = date('Y-m-d');
                } else {
                    $tgl = explode(' - ', urldecode($this->input->get('tanggal')));
                    $ts = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[0])));
                    $te = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[1])));
                }
                $admin = $this->input->get('admin');
                $admindtl = $this->db->from('zx_xvrty')
                    ->join('m_daerah', 'usr_dae_id = dae_id', 'left')
                    ->where("usr_nama = '$admin'")
                    ->get()->row_array();
                $listdata = $this->Laporanmodel->li_penjualan_admin_excel($admin, $ts, $te);
                $dtproduk = $listdata['list'];
                $dtheader = $listdata['bulan'];

                // print_r($dtproduk);
                // die;

                $this->load->library('PHPExcel');
                $excel = new PHPExcel();
                $sh1 = $excel->setActiveSheetIndex(0);

                $titleStyle = new PHPExcel_Style();
                $titlesubStyle = new PHPExcel_Style();
                $detailStyle = new PHPExcel_Style();
                $headerStyle = new PHPExcel_Style();
                $bodyStyle = new PHPExcel_Style();
                $centerStyle = new PHPExcel_Style();
                $rightStyle = new PHPExcel_Style();
                $centerBoldStyle = new PHPExcel_Style();
                $rightBoldStyle = new PHPExcel_Style();
                $titleStyle->applyFromArray(
                    array(
                        'font' => array(
                            'bold' => true,
                            'italic' => true,
                            'size' => 10,
                            'name' => 'Arial'
                        ),
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                            'rotation' => 0
                        ),
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'FFFF00')
                        )
                    )
                );
                $titlesubStyle->applyFromArray(
                    array(
                        'font' => array(
                            'bold' => false,
                            'size' => 11,
                            'name' => 'Calibri'
                        ),
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                            'rotation' => 0
                        )
                    )
                );
                $detailStyle->applyFromArray(
                    array(
                        'font' => array(
                            'bold' => false,
                            'size' => 16,
                            'name' => 'Calibri'
                        ),
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                            'rotation' => 0
                        )
                    )
                );
                $headerStyle->applyFromArray(
                    array(
                        'font' => array(
                            'bold' => true,
                            'italic' => true,
                            'size' => 10,
                            'name' => 'Arial'
                        ),
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                            'rotation' => 0
                        ),
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'FFFF00')
                        ),
                        'borders' => array(
                            'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                        )
                    )
                );
                $bodyStyle->applyFromArray(
                    array(
                        'font' => array(
                            'bold' => true,
                            'italic' => true,
                            'size' => 10,
                            'name' => 'Arial'
                        ),
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                            'rotation' => 0,
                            'wrap' => TRUE
                        ),
                        'borders' => array(
                            'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                        )
                    )
                );
                $centerStyle->applyFromArray(
                    array(
                        'font' => array(
                            'bold' => true,
                            'italic' => true,
                            'size' => 10,
                            'name' => 'Arial'
                        ),
                        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                        'borders' => array(
                            'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                        )
                    )
                );
                $centerBoldStyle->applyFromArray(
                    array(
                        'font' => array(
                            'bold' => true,
                            'size' => 10,
                            'name' => 'Arial'
                        ),
                        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('argb' => 'FFEEEEEE')
                        ),
                        'borders' => array(
                            'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                        )
                    )
                );
                $rightStyle->applyFromArray(
                    array(
                        'font' => array(
                            'bold' => true,
                            'italic' => true,
                            'size' => 10,
                            'name' => 'Arial'
                        ),
                        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT),
                        'borders' => array(
                            'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                        )
                    )
                );
                $rightBoldStyle->applyFromArray(
                    array(
                        'font' => array(
                            'bold' => true,
                            'size' => 10,
                            'name' => 'Arial'
                        ),
                        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT),
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('argb' => 'FFEEEEEE')
                        ),
                        'borders' => array(
                            'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                            'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                        )
                    )
                );

                $sh1->setCellValue('A1', 'NAMA ADMIN/SALES ');
                $excel->getActiveSheet()->mergeCells('A1:B1');
                $sh1->setCellValue('C1', ': ' . $admin);
                $excel->getActiveSheet()->mergeCells('C1:F1');
                $sh1->setCellValue('A2', 'WILAYAH ');
                $excel->getActiveSheet()->mergeCells('A2:B2');
                $sh1->setCellValue('C2', ': ' . $admindtl['dae_nama']);
                $excel->getActiveSheet()->mergeCells('C2:F2');
                $sh1->setCellValue('A3', 'TANGGAL ');
                $excel->getActiveSheet()->mergeCells('A3:B3');
                $sh1->setCellValue('C3', ': ' . date('d-m-Y', strtotime($ts)) . ' s/d ' . date('d-m-Y', strtotime($te)));
                $excel->getActiveSheet()->mergeCells('C3:F3');

                //header
                $sh1->setCellValue('A4', 'NO');
                $excel->getActiveSheet()->mergeCells('A4:A5');
                $sh1->setCellValue('B4', 'NAMA PRODUK');
                $excel->getActiveSheet()->mergeCells('B4:B5');
                $sh1->setCellValue('C4', 'HARGA');
                $excel->getActiveSheet()->mergeCells('C4:C5');
                $colheader = 'B';
                $colheadermerge = 'C';
                foreach ($dtheader as $d) {
                    $colheader++;
                    $colheader++;
                    $colheadermerge++;
                    $colheadermerge++;
                    $sh1->setCellValue($colheader . "4", $this->ascfunc->bulan_[$d['bulan']] . "-" . $d['tahun']);
                    $sh1->setCellValue($colheader . "5", 'U');
                    $sh1->setCellValue($colheadermerge . "5", 'V');
                    $excel->getActiveSheet()->mergeCells($colheader . "4:" . $colheadermerge . "4");
                    $excel->getActiveSheet()->getColumnDimension($colheader)->setWidth(5);
                    $excel->getActiveSheet()->getColumnDimension($colheadermerge)->setWidth(12);
                }
                $no = 1;
                $row = 6;
                foreach ($dtproduk as $d) {
                    $sh1->setCellValue('A' . $row, $no);
                    $sh1->setCellValue('B' . $row, $d['produk']);
                    $sh1->setCellValue('C' . $row, $d['harga']);
                    $col = 'D';
                    foreach ($dtheader as $dh) {
                        if (!isset($d['data'][$dh['bulan'] . '-' . $dh['tahun']])) {
                            $sh1->setCellValue($col . $row, '-');
                            $excel->getActiveSheet()->setSharedStyle($centerStyle, $col . $row);
                            $col++;
                            $sh1->setCellValue($col . $row, '-');
                            $excel->getActiveSheet()->setSharedStyle($rightStyle, $col . $row);
                            $col++;
                        } else {
                            $sh1->setCellValue($col . $row, $d['data'][$dh['bulan'] . '-' . $dh['tahun']]['jumlah']);
                            $excel->getActiveSheet()->setSharedStyle($centerStyle, $col . $row);
                            $col++;
                            $sh1->setCellValue($col . $row, $d['data'][$dh['bulan'] . '-' . $dh['tahun']]['total']);
                            $excel->getActiveSheet()->setSharedStyle($rightStyle, $col . $row);
                            $col++;
                        }
                    }
                    $row++;
                    $no++;
                }

                $sh1->setCellValue('A' . $row, 'TOTAL PENJUALAN');
                $excel->getActiveSheet()->mergeCells('A' . $row . ':C' . $row);
                for ($i = 'E'; $i <= $colheadermerge; $i++) {
                    $sh1->setCellValue($i . $row, "=SUM(" . $i . "6:" . $i . ($row - 1) . ")");
                    $i++;
                }

                $excel->getActiveSheet()->setSharedStyle($titleStyle, "A1:F3");
                $excel->getActiveSheet()->setSharedStyle($headerStyle, "A4:" . $colheadermerge . "5");
                $excel->getActiveSheet()->setSharedStyle($bodyStyle, "A6:B" . $row);
                $excel->getActiveSheet()->setSharedStyle($rightStyle, "C6:C" . $row);
                $excel->getActiveSheet()->setSharedStyle($centerStyle, "A" . $row . ":C" . $row);
                $excel->getActiveSheet()->setSharedStyle($rightStyle, "D" . $row . ":" . $colheadermerge . $row);

                $excel->getActiveSheet()->getStyle("C6:" . $colheadermerge . $row)->getNumberFormat()->setFormatCode("#,##0");

                //note
                $sh1->setCellValue('B' . ($row + 1), 'NOTE');
                $excel->getActiveSheet()->mergeCells('B' . ($row + 1) . ':C' . ($row + 1));
                $sh1->setCellValue('B' . ($row + 2), 'U = UNIT/JUMLAH');
                $excel->getActiveSheet()->mergeCells('B' . ($row + 2) . ':C' . ($row + 2));
                $sh1->setCellValue('B' . ($row + 3), 'V = NILAI/HARGA BARANG');
                $excel->getActiveSheet()->mergeCells('B' . ($row + 3) . ':C' . ($row + 3));

                $excel->getActiveSheet()->setSharedStyle($titleStyle, 'B' . ($row + 1) . ':C' . ($row + 3));



                $excel->getActiveSheet()->getColumnDimension('A')->setWidth(3.25);
                $excel->getActiveSheet()->getColumnDimension('B')->setWidth(53);
                $excel->getActiveSheet()->getColumnDimension('C')->setWidth(12);




                $excel->getActiveSheet()->setTitle('LAPORAN PENJUALAN');
                $excel->setActiveSheetIndex(0);

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename=PENJUALAN_' . $admin . '_' . date('d-m-Y', strtotime($ts)) . ' ~ ' . date('d-m-Y', strtotime($te)) . '.xlsx');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
                $objWriter->save('php://output');
            }
            function adminsalespdf_()
            {
                if (!$this->session->userdata('log_user')) {
                    redirect(base_url('login'));
                }
                if ($this->input->get('tanggal') == '') {
                    $ts = $te = date('Y-m-d');
                } else {
                    $tgl = explode(' - ', urldecode($this->input->get('tanggal')));
                    $ts = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[0])));
                    $te = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[1])));
                }
                $admin = $this->input->get('admin');
                $data = $this->adminsalescontent_($admin, $ts, $te);
                // $gdg = $this->Umodel->get_data('m_gudang', array('gdg_id' => $admin))->row_array();
                $data['admin'] = $admin;
                $data['info'] = $this->ascfunc->info_();
                $data['tanggal'] = date('d-m-Y', strtotime($ts)) . ' s/d ' . date('d-m-Y', strtotime($te));
                $this->load->helper(array('dompdf', 'file'));
                $html = $this->load->view('pdf/adminsales', $data, true);
                pdf_create($html, 'LAPORAN_PENJUALAN_[' . date('d-m-Y', strtotime($ts)) . ' s/d ' . date('d-m-Y', strtotime($te)) . ']', "potrait");
            }
            function penjualanpdf_()
            {
                if (!$this->session->userdata('log_user')) {
                    redirect(base_url('login'));
                }
                if ($this->input->get('tanggal') == '') {
                    $ts = $te = date('Y-m-d');
                } else {
                    $tgl = explode(' - ', urldecode($this->input->get('tanggal')));
                    $ts = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[0])));
                    $te = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[1])));
                }
                $toko = $this->input->get('toko');
                $data = $this->penjualancontent_($toko, $ts, $te);
                $gdg = $this->Umodel->get_data('m_gudang', array('gdg_id' => $toko))->row_array();
                $data['toko'] = $gdg['gdg_nama'];
                $data['info'] = $this->ascfunc->info_();
                $data['tanggal'] = date('d-m-Y', strtotime($ts)) . ' s/d ' . date('d-m-Y', strtotime($te));
                $this->load->helper(array('dompdf', 'file'));
                $html = $this->load->view('pdf/penjualan', $data, true);
                pdf_create($html, 'LAPORAN_PENJUALAN_[' . date('d-m-Y', strtotime($ts)) . ' s/d ' . date('d-m-Y', strtotime($te)) . ']', "potrait");
            }
            function pembelianpdf_()
            {
                if (!$this->session->userdata('log_user')) {
                    redirect(base_url('login'));
                }
                if ($this->input->get('tanggal') == '') {
                    $ts = $te = date('Y-m-d');
                } else {
                    $tgl = explode(' - ', urldecode($this->input->get('tanggal')));
                    $ts = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[0])));
                    $te = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[1])));
                }
                $supplier = $this->input->get('supplier');
                $data = $this->pembeliancontent_($supplier, $ts, $te);
                if ($supplier == '') {
                    $data['supplier'] = 'SEMUA SUPPLIER';
                } else {

                    $sup = $this->Umodel->get_data('m_supplier', array('sup_id' => $supplier))->row_array();
                    $data['supplier'] = $sup['sup_nama'];
                }
                $data['info'] = $this->ascfunc->info_();
                $data['tanggal'] = date('d-m-Y', strtotime($ts)) . ' s/d ' . date('d-m-Y', strtotime($te));
                $this->load->helper(array('dompdf', 'file'));
                $html = $this->load->view('pdf/pembelian', $data, true);
                pdf_create($html, 'LAPORAN_PEMBELIAN_[' . date('d-m-Y', strtotime($ts)) . ' s/d ' . date('d-m-Y', strtotime($te)) . ']', "potrait");
            }

            public function penjualanproduk()
            {
                if (!$this->session->userdata('log_user')) {
                    redirect(base_url('login'));
                }
                $allowedlevel = array(1, 2, 3);
                if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
                    show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
                    exit();
                }
                $header = $this->ascfunc->header_('lappenjualanproduk-menu');
                $header['css'] = array('assets/plugin/datepicker/bootstrap-datepicker.css', 'assets/plugin/daterangepicker/daterangepicker-bs3.css');
                $header['js'] = array('assets/js/bootstrap-typeahead.js', 'assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js', 'assets/plugin/moment/moment.js', 'assets/plugin/daterangepicker/daterangepicker.js');
                $body = array();
                $this->load->view('templates/header', $header);
                $this->load->view('laporan/penjualanproduk', $body);
                $this->load->view('templates/footer');
            }

            function cari_()
            {
                $search = strtolower($this->input->post('w'));
                $sql = $this->Umodel->find_data('m_produk', array('LOWER(prd_nama)' => $search));
                $dt = array();
                foreach ($sql as $val) {
                    $data['id'] = $val['prd_id'];
                    $data['content'] = $val['prd_nama'];
                    array_push($dt, $data);
                }
                echo json_encode($dt);
            }

            private function penjualanprodukcontent_($idproduk, $tstart, $tend)
            {
                $data = $this->Laporanmodel->li_penjualanproduk($idproduk, $tstart, $tend);
                $produk = $this->db->from('m_produk')
                    ->join('m_supplier', 'prd_sup_id = sup_id')
                    ->where('prd_id', $idproduk)
                    ->get()
                    ->row_array();
                ob_start();
                $total = $totalp = 0;
                if (count($data) > 0) {
                    $no = 1;
                    foreach ($data as $val) {
                        $tanggal = date('d/m/Y', strtotime($val['nota_tanggal']));
                        $pemilik = ($val['cus_iskios']) ? '<br>' . $val['cus_pemilik'] : '';
                        $customer = ($val['nota_tujuan'] == 0) ? 'Umum' : $val['cus_nama'] . $pemilik;
                        $jumlah = $val['dtn_jumlah'] * $val['dtn_hargajual'];
                        ?>
                <tr>
                    <td class="text-center"><?= $no ?></td>
                    <td><?= $val['prd_nama'] ?></td>
                    <td class="text-center"><?= $tanggal ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['dtn_jumlah']) ?></td>
                    <td><?= $customer ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($jumlah) ?></td>
                </tr>
            <?php
                            $total += $val['dtn_jumlah'];
                            $totalp += $jumlah;
                            $no++;
                        }
                    } else {
                        ?>
            <tr>
                <td class="text-center" colspan="6">Tidak ada data...</td>
            </tr>
        <?php
                }
                $result['tbody'] = ob_get_contents();
                ob_clean();
                ?>
        <tr>
            <td style="width: 20%">NAMA BARANG</td>
            <td style="width: 80%">: <?= $produk['prd_nama'] ?></td>
        </tr>
        <tr>
            <td>ID BARANG</td>
            <td>: <?= $produk['prd_kode'] ?></td>
        </tr>
        <tr>
            <td>ASAL PRODUSEN</td>
            <td>: <?= $produk['sup_nama'] ?></td>
        </tr>
        <?php
                $result['detail'] = ob_get_contents();
                ob_clean();
                ?>
        <tr>
            <td style="width: 20%">Total Rekap</td>
            <td style="width: 80%">: <?= $this->ascfunc->nf_($total) ?></td>
        </tr>
        <tr>
            <td>Total Pendapatan</td>
            <td>: Rp. <?= $this->ascfunc->nf_($totalp) ?></td>
        </tr>
        <?php
                $result['rekap'] = ob_get_contents();
                ob_clean();
                return $result;
            }

            function penjualanprodukfilter_()
            {
                if ($this->input->post('tanggal') == '') {
                    $ts = $te = date('Y-m-d');
                } else {
                    $tgl = explode(' - ', $this->input->post('tanggal'));
                    $ts = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[0])));
                    $te = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[1])));
                }
                $idproduk = $this->input->post('id');
                $arr = $this->penjualanprodukcontent_($idproduk, $ts, $te);
                echo json_encode($arr);
            }

            function penjualanprodukpdf_()
            {
                if (!$this->session->userdata('log_user')) {
                    redirect(base_url('login'));
                }
                if ($this->input->get('tanggal') == '') {
                    $ts = $te = date('Y-m-d');
                } else {
                    $tgl = explode(' - ', urldecode($this->input->get('tanggal')));
                    $ts = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[0])));
                    $te = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[1])));
                }
                $idproduk = $this->input->get('id');
                $prd = $this->Umodel->get_data('m_produk', array('prd_id' => $idproduk))->row_array();
                $data = $this->penjualanprodukcontent_($idproduk, $ts, $te);
                $data['info'] = $this->ascfunc->info_();
                $data['tanggal'] = date('d-m-Y', strtotime($ts)) . ' s/d ' . date('d-m-Y', strtotime($te));

                $this->load->helper(array('dompdf', 'file'));
                $html = $this->load->view('pdf/penjualanproduk', $data, true);
                pdf_create($html, 'LAPORAN_DETAIL_BARANG_' . $prd['prd_kode'] . '_[' . date('d-m-Y', strtotime($ts)) . ' s/d ' . date('d-m-Y', strtotime($te)) . ']', "potrait");
            }

            public function ppn()
            {
                if (!$this->session->userdata('log_user')) {
                    redirect(base_url('login'));
                }
                $allowedlevel = array(1, 2, 3);
                if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
                    show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
                    exit();
                }
                $header = $this->ascfunc->header_('lapppn-menu');
                $header['css'] = array('assets/plugin/datepicker/bootstrap-datepicker.css', 'assets/plugin/daterangepicker/daterangepicker-bs3.css');
                $header['js'] = array('assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js', 'assets/plugin/moment/moment.js', 'assets/plugin/daterangepicker/daterangepicker.js');
                $body = array(
                    'opttoko' => $this->opttoko()
                );
                $this->load->view('templates/header', $header);
                $this->load->view('laporan/ppn', $body);
                $this->load->view('templates/footer');
            }

            function ppnfilter_()
            {
                if ($this->input->post('tanggal') == '') {
                    $ts = $te = date('Y-m-d');
                } else {
                    $tgl = explode(' - ', $this->input->post('tanggal'));
                    $ts = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[0])));
                    $te = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[1])));
                }
                $toko = $this->input->post('toko');
                $jenis = $this->input->post('jenis');
                $arr = $this->ppncontent_($jenis, $toko, $ts, $te);
                echo json_encode($arr);
            }

            private function ppncontent_($jenis, $toko, $tstart, $tend)
            {
                $data = $this->Laporanmodel->li_penjualanppn($jenis, $toko, $tstart, $tend);
                ob_start();
                $total = 0;
                if (count($data) > 0) {
                    $no = 1;
                    foreach ($data as $val) {
                        ?>
                <tr>
                    <td class="text-center"><?= $no ?></td>
                    <td><?= $val['prd_nama'] ?></td>
                    <td class="text-center"><?= $val['prd_satuan'] ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['jumlah']) ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['total']) ?></td>
                </tr>
            <?php
                            $no++;
                            $total += $val['total'];
                        }
                    } else {
                        ?>
            <tr>
                <td class="text-center" colspan="10">Tidak ada data...</td>
            </tr>
        <?php
                }
                $result['tbody'] = ob_get_contents();
                ob_clean();
                ?>
        <tr style="font-weight: bold">
            <td class="text-center" colspan="4">Total</td>
            <td class="text-right"><?= $this->ascfunc->nf_($total) ?></td>
        </tr>
<?php
        $result['tfoot'] = ob_get_contents();
        ob_clean();
        return $result;
    }

    function ppnpdf_()
    {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        if ($this->input->get('tanggal') == '') {
            $ts = $te = date('Y-m-d');
        } else {
            $tgl = explode(' - ', urldecode($this->input->get('tanggal')));
            $ts = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[0])));
            $te = date('Y-m-d', strtotime(str_replace('/', '-', $tgl[1])));
        }
        $toko = $this->input->get('toko');
        $jenis = $this->input->get('jenis');
        $jns = ($jenis) ? 'PPN' : 'Non-PPN';
        $data = $this->ppncontent_($jenis, $toko, $ts, $te);
        $gdg = $this->Umodel->get_data('m_gudang', array('gdg_id' => $toko))->row_array();
        $data['toko'] = $gdg['gdg_nama'];
        $data['jenis'] = $jns;
        $data['info'] = $this->ascfunc->info_();
        $data['tanggal'] = date('d-m-Y', strtotime($ts)) . ' s/d ' . date('d-m-Y', strtotime($te));
        $this->load->helper(array('dompdf', 'file'));
        $html = $this->load->view('pdf/ppn', $data, true);
        pdf_create($html, 'LAPORAN_' . $jns . '_[' . date('d-m-Y', strtotime($ts)) . ' s/d ' . date('d-m-Y', strtotime($te)) . ']', "potrait");
    }
}
