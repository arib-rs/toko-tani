<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Laporanmodel');
    }

    public function index() {
        show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
        exit();
    }

    public function opname() {
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

    function optgudang($selected = '') {
        $data = $this->Umodel->li_data('m_gudang', 'gdg_nama');
        $opt = '<option value="0">-Pilih Lokasi-</option>';
        foreach ($data->result_array() as $value) {
            $sel = ($selected == $value['gdg_id']) ? 'selected=""' : '';
            $opt .= '<option ' . $sel . ' value="' . $value['gdg_id'] . '">' . $value['gdg_nama'] . '</option>';
        }
        return $opt;
    }

    private function opnamecontent_($gudang, $tanggal) {
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
                    <td><?= $val['prd_nama'] ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['stok']) ?></td>
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
                <td class="text-center" colspan="7">Tidak ada data...</td>
            </tr>
            <?php
        }
        $result['tbody'] = ob_get_contents();
        ob_clean();
        ?>
        <tr style="font-weight: bold">
            <td class="text-center" colspan="5">Total</td>
            <td class="text-right"><?= $this->ascfunc->nf_($total) ?></td>
            <td></td>
        </tr>
        <?php
        $result['tfoot'] = ob_get_contents();
        ob_clean();
        return $result;
    }

    function opnamefilter_() {
        $tgl = $this->input->post('tanggal');
        $gudang = $this->input->post('gudang');
        $tanggal = ($tgl == '') ? date('Y-m-d') : date('Y-m-d', strtotime(str_replace('/', '-', $tgl)));
        $arr = $this->opnamecontent_($gudang, $tanggal);
        echo json_encode($arr);
    }

    function opnamepdf_() {
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

    public function operasional() {
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
        $body = array(
        );
        $this->load->view('templates/header', $header);
        $this->load->view('laporan/operasional', $body);
        $this->load->view('templates/footer');
    }

    private function operasionalcontent_($tstart, $tend) {
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

    function operasionalfilter_() {
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

    function operasionalpdf_() {
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

    public function penjualan() {
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

    function opttoko($selected = '') {
        $data = $this->Umodel->get_data('m_gudang', array('gdg_isjual' => 1), 'gdg_nama');
        $opt = '';
        foreach ($data->result_array() as $value) {
            $sel = ($selected == $value['gdg_id']) ? 'selected=""' : '';
            $opt .= '<option ' . $sel . ' value="' . $value['gdg_id'] . '">' . $value['gdg_nama'] . '</option>';
        }
        return $opt;
    }

    private function penjualancontent_($toko, $tstart, $tend) {
        $data = $this->Laporanmodel->li_penjualan($toko, $tstart, $tend);
        ob_start();
        $thb = $thj = $tprf = 0;
        if (count($data) > 0) {
            foreach ($data as $val) {
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
                    <td class="text-right"><?= $this->ascfunc->nf_($val['hargajual'] - $val['hargabeli']) ?></td>
                    <td class="text-right"><?= $this->ascfunc->nf_($val['jumlah'] * ($val['hargajual'] - $val['hargabeli'])) ?></td>
                </tr>
                <?php
                $thb += $val['jumlah'] * $val['hargabeli'];
                $thj += $val['jumlah'] * $val['hargajual'];
                $tprf += $val['jumlah'] * ($val['hargajual'] - $val['hargabeli']);
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
            <td class="text-center" colspan="5">Total</td>
            <td class="text-right"><?= $this->ascfunc->nf_($thb) ?></td>
            <td></td>
            <td class="text-right"><?= $this->ascfunc->nf_($thj) ?></td>
            <td></td>
            <td class="text-right"><?= $this->ascfunc->nf_($tprf) ?></td>
        </tr>
        <?php
        $result['tfoot'] = ob_get_contents();
        ob_clean();
        return $result;
    }

    function penjualanfilter_() {
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

    function penjualanpdf_() {
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

    public function penjualanproduk() {
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
        $body = array(
        );
        $this->load->view('templates/header', $header);
        $this->load->view('laporan/penjualanproduk', $body);
        $this->load->view('templates/footer');
    }

    function cari_() {
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

    private function penjualanprodukcontent_($idproduk, $tstart, $tend) {
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

    function penjualanprodukfilter_() {
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

    function penjualanprodukpdf_() {
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

    public function ppn() {
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

    function ppnfilter_() {
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

    private function ppncontent_($jenis, $toko, $tstart, $tend) {
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

    function ppnpdf_() {
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
