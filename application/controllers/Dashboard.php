<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashboardmodel');
    }

    public function index()
    {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $header = $this->ascfunc->header_('dashboard-menu');
        $header['css'] = array('assets/plugin/highcharts/css/highcharts.css', 'assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/plugin/highcharts/js/highcharts.js', 'assets/plugin/highcharts/modules/exporting.js', 'assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $toplevel = array(1, 2, 3);
        $oplevel = array(4);
        if (in_array($this->session->userdata('log_level'), $toplevel)) {
            $body = array(
                'rekening' => $this->cbrek()
            );
            $this->load->view('templates/header', $header);
            $this->load->view('dashboard', $body);
        } else if (in_array($this->session->userdata('log_level'), $oplevel)) {
            $this->load->view('templates/header', $header);
            $this->load->view('dashboardop');
        }
        $this->load->view('templates/footer');
    }

    function filterbox_($tanggal = '', $rekening = 0)
    {
        if ($tanggal == '') {
            $start = date('Y-m') . '-01';
            $end = date('Y-m-t');
            $get = date('m/Y');
        } else {
            $tgl = explode(' ', urldecode($tanggal));
            $b = array_search($tgl[0], $this->ascfunc->bulan_);
            $start = $tgl[1] . '-' . $b . '-01';
            $end = date('Y-m-t', strtotime($start));
            $get = $b . '/' . $tgl[1];
        }
        $arr['box'] = $this->box($start, $end, $rekening, $get);
        echo json_encode($arr);
    }

    private function box($start, $end, $rekening, $get)
    {
        $this->load->model('Kasmodel');
        $this->load->model('Keuanganmodel');

        $toplevel = array(1, 3);
        $data = $this->Dashboardmodel->get_box($start, $end, $rekening);
        $link = ($this->session->userdata('log_level') == 3) ? '/keu' : '';
        $saldoawal = $this->Kasmodel->saldoawal($start, $rekening);
        $kas = $this->Kasmodel->rekapkas($start, $end, $rekening);
        $expStart = date('Y-m-d');
        $expEnd = date('Y-m-d', strtotime($expStart . ' + 6 month'));

        $piutangjatuhtempo = $this->Keuanganmodel->li_piutangcustomerdashboard();



        $tdebet = $tkredit = 0;
        foreach ($kas as $val) {
            $tdebet += $val['kas_debet'];
            $tkredit += $val['kas_kredit'];
        }
        $labarugi = (abs($saldoawal['debet']) + $tdebet) - (abs($saldoawal['kredit']) + $tkredit);
        ob_start();
        ?>
        <div class="col-lg-4 col-xs-4">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?= $this->ascfunc->nf_($labarugi); ?></h3>
                    <p>Laba Rugi </p>
                </div>
                <div class="icon">
                    <i class="fa fa-bar-chart"></i>
                </div>
                <a href="<?= base_url('kas') . "?filter='$get'" ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-xs-4">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?= $this->ascfunc->nf_($tdebet); ?></h3>
                    <p>Pemasukan</p>
                </div>
                <div class="icon">
                    <i class="fa fa-plus-circle"></i>
                </div>
                <a href="<?= base_url('kas') . "?filter='$get'" ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-xs-4">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?= $this->ascfunc->nf_($tkredit); ?></h3>
                    <p>Pengeluaran</p>
                </div>
                <div class="icon">
                    <i class="fa fa-minus-circle"></i>
                </div>
                <a href="<?= base_url('kas') . "?filter='$get'" ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <?php if (in_array($this->session->userdata('log_level'), $toplevel)) { ?>
            <div class="clearfix"></div>
            <div class="col-lg-3 col-xs-3">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><?= $data['penjualan'] ?></h3>
                        <p>Penjualan</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-database"></i>
                    </div>
                    <a href="<?= base_url('penjualan') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-3">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3><?= $data['penjualanbl'] ?></h3>
                        <p>Penjualan Belum Lunas</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-exclamation-circle"></i>
                    </div>
                    <a href="<?= base_url('piutang') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-3">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><?= $data['pembelian'] ?></h3>
                        <p>Pembelian</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-database"></i>
                    </div>
                    <a href="<?= base_url('barangmasuk') . $link ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-3">
                <div class="small-box bg-purple">
                    <div class="inner">
                        <h3><?= $data['customer'] ?></h3>
                        <p>Customer</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <a href="<?= base_url('customer') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

        <?php
                }

                $start1 = str_replace("01", "31", $start);
                $produkexp = $this->db->select('m_produk.*, m_supplier.*,m_gudang.*, SUM(stk_jumlah) as stok,stk_kadaluarsa,stk_nobatch')
                    ->from('m_produk')
                    ->join('d_stok', 'prd_id = stk_prd_id')
                    ->join('m_supplier', 'sup_id = prd_sup_id')
                    ->join('m_gudang', 'gdg_id = stk_gdg_id')
                    // ->where("stk_kadaluarsa BETWEEN '$start' AND '$start1'")
                    ->where("stk_kadaluarsa BETWEEN '$expStart' AND '$expEnd'")
                    ->group_by('prd_id')
                    ->group_by('stk_gdg_id')
                    // ->order_by('prd_nama')
                    ->order_by('stk_kadaluarsa')
                    ->having('stok > 0')
                    ->get()
                    ->result_array();

                ?>
        <br>

        <div class="col-md-12 col-xs-12">

            <div class="box">
                <div class="box-header">
                    <!-- <h3 class="box-title"><b>PRODUK EXPIRED <?php echo $start . ' S/D ' . $start1 ?></b></h3> -->
                    <h3 class="box-title"><b>PRODUK EXPIRED <?php echo $expStart . ' S/D ' . $expEnd ?></b></h3>
                    <div class="box-body no-padding">
                        <table id="example1" class="table table-bordered">
                            <thead class="bg-gray-active">
                                <tr>
                                    <th class="text-center" style="width: 5%">No</th>
                                    <th class="text-center" style="width: 10%">Kode</th>
                                    <th class="text-center" style="width: 10%">No.Batch</th>
                                    <th class="text-center" style="width: 25%">GUDANG</th>
                                    <th class="text-center" style="width: 25%">Nama</th>
                                    <th class="text-center" style="width: 10%">Stok</th>
                                    <th class="text-center" style="width: 10%">Expired</th>
                                    <th class="text-center" style="width: 20%">Supplier</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                        $no = 1;
                                        foreach ($produkexp as $val) {

                                            ?>
                                    <tr>
                                        <td class="text-center"><?= $no ?></td>
                                        <td class="text-center"><?= $val['prd_kode'] ?></td>
                                        <td class="text-center"><?= $val['stk_nobatch'] ?></td>
                                        <td><?= $val['gdg_nama'] ?></td>
                                        <td><?= $val['prd_nama'] ?></td>
                                        <td class="text-right"><?= $this->ascfunc->nf_($val['stok']) ?></td>
                                        <td class="text-center"><?= date('d/m/Y', strtotime($val['stk_kadaluarsa'])) ?></td>
                                        <td><?= $val['sup_nama'] ?></td>
                                    </tr>
                                <?php

                                            $no++;
                                        }
                                        ?>
                            </tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-xs-12">

            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><b>REMINDER PIUTANG CUSTOMER</h3>
                    <div class="box-body no-padding">
                        <table id="example3" class="table table-bordered">
                            <thead class="bg-gray-active">
                                <tr>
                                    <th style="width: 3%" class="text-center">No</th>
                                    <th style="width: 30%" class="text-center">Customer</th>
                                    <th style="width: 50%" class="text-center">Detail</th>
                                    <th style="width: 17%" class="text-center">Total Piutang</th>
                                </tr>
                            </thead>
                            <tbody style="font-weight:normal">
                                <?php
                                        $no = 1;
                                        foreach ($piutangjatuhtempo as $vd) {
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
                                                            $li .= "<li style='margin-bottom:20px;'>No. Faktur : " . $vp['id'] . "<br>Tanggal : " . $vp['tanggal'] . "<br><b>Jatuh Tempo : " . $vp['jatuhtempo'] . "</b><br>Piutang : Rp. " . $this->ascfunc->nf_($vp['piutang']) . "</li>";
                                                            $total += $vp['piutang'];
                                                        }
                                                        $li .= '</ol>';
                                                        echo $li;
                                                        ?>
                                        </td>
                                        <td align="right"><?= 'Rp. ' . $this->ascfunc->nf_($total) ?></td>
                                    </tr>
                                <?php

                                            $no++;
                                        }
                                        ?>
                            </tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <?php
                $box = ob_get_contents();
                ob_clean();
                return $box;
            }

            private function cbrek()
            {
                $data = $this->Umodel->li_data('m_rekening');
                $check = TRUE;
                ob_start();
                foreach ($data->result_array() as $value) {
                    $checked = ($check) ? 'checked=""' : '';
                    ?>
            <label class="radio-inline">
                <input <?= $checked ?> type="radio" name="rekening" id="<?= $value['rek_nomor'] ?>" value="<?= $value['rek_nomor'] ?>"> <?= $value['rek_nomor'] ?>
            </label>
<?php
            $check = FALSE;
        }
        $cb = ob_get_contents();
        ob_clean();
        return $cb;
    }

    function filterkas_($tanggal = '', $rekening = 0)
    {
        if ($tanggal == '') {
            $start = date('Y-m') . '-01';
            $end = date('Y-m-t');
        } else {
            $tgl = explode(' ', urldecode($tanggal));
            $b = array_search($tgl[0], $this->ascfunc->bulan_);
            $start = $tgl[1] . '-' . $b . '-01';
            $end = date('Y-m-t', strtotime($start));
        }
        $arr = $this->grafikkas($start, $end, $rekening);
        echo json_encode($arr);
    }

    private function grafikkas($start, $end, $rekening)
    {
        $data = $this->Dashboardmodel->get_kas($start, $end, $rekening);
        $this->load->model('Kasmodel');
        $saldo = $this->Kasmodel->saldoawal($start, $rekening);
        $debet = $kredit = $categories = $series = array();
        $categories[] = 'Saldo';
        $debet[] = (float) $saldo['debet'];
        $kredit[] = (float) -1 * $saldo['kredit'];
        foreach ($data as $val) {
            $categories[] = date('d/m', strtotime($val['kas_tanggal']));
            $debet[] = (float) $val['debet'];
            $kredit[] = (float) $val['kredit'];
        }
        $series[] = array(
            'name' => 'Debet',
            'data' => $debet
        );
        $series[] = array(
            'name' => 'Kredit',
            'data' => $kredit
        );
        $arr['categories'] = $categories;
        $arr['series'] = $series;
        return $arr;
    }
}
