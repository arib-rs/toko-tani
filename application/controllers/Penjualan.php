<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

class Penjualan extends CI_Controller
{

    var $len = 33;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Transaksimodel');
    }

    public function index()
    {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $header = $this->ascfunc->header_('penjualan-menu');
        $header['css'] = array('assets/plugin/datetimepicker/css/bootstrap-datetimepicker.min.css', 'assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/js/bootstrap-typeahead.js', 'assets/js/jquery.maskMoney.js', 'assets/plugin/moment/moment.min.js', 'assets/plugin/datetimepicker/js/bootstrap-datetimepicker.min.js', 'assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $toplevel = array(1, 2, 3);
        $oplevel = array(5);
        // print_r($this->session->userdata());
        // die;
        if (in_array($this->session->userdata('log_level'), $toplevel)) {
            $this->load->view('templates/header', $header);
            $body = array(
                'gudang' => $this->optgudang()
            );
            $this->load->view('transaksi/penjualantop', $body);
        } else if (in_array($this->session->userdata('log_level'), $oplevel)) {
            $sys = $this->ascfunc->sys_();
            $header['ppn'] = $sys['ppn'];
            $body = array(
                'optcustomer' => $this->optcustomer(),
                'optkategori' => $this->optkategori(),
                'cbrek' => $this->cbrek_(),
                'trop' => $this->trop_(),
                'gudang' => $this->session->userdata('log_gudang')
            );
            $this->load->view('templates/header', $header);
            $this->load->view('transaksi/penjualan', $body);
        }
        $this->load->view('templates/footer');
    }

    function cari_()
    {
        $search = strtolower($this->input->post('word'));
        $harga = $this->input->post('harga');
        $gudang = $this->session->userdata('log_gudang');
        $sql = $this->db->select('m_produk.*, SUM(stk_jumlah) as stok,stk_kadaluarsa,stk_nobatch')
            ->from('m_produk')
            ->join('d_stok', 'prd_id = stk_prd_id')
            ->where('stk_gdg_id', $gudang)
            ->like('lower(prd_nama)', $search)
            ->group_by('prd_id')
            ->having('stok > 0')
            ->get();
        $dt = array();
        foreach ($sql->result_array() as $val) {
            $data['content'] = $val['prd_nama'] . ' [' . $val['prd_satuan'] . '] | Exp: ' . $val['stk_kadaluarsa'];
            $data['id'] = $val['prd_id'];
            $data['kode'] = $val['prd_kode'];
            $data['nama'] = $val['prd_nama'];
            $data['satuan'] = $val['prd_satuan'];
            $data['hb'] = $val['prd_hargabeli'];
            $data['hj'] = $val['prd_' . $harga];
            $data['stok'] = $val['stok'];
            $data['kadaluarsa'] = $val['stk_kadaluarsa'];
            $data['nobatch'] = $val['stk_nobatch'];
            array_push($dt, $data);
        }
        echo json_encode($dt);
    }

    function optcustomer()
    {
        $data = $this->Umodel->li_data('m_customer', 'cus_nama');
        $opt = '<option value="0" data-harga="hargaecer">Umum</option>';
        foreach ($data->result_array() as $value) {
            if ($value['cus_dae_id'] == $this->session->userdata('log_daerah')) {

                $pemilik = ($value['cus_iskios']) ? ' - ' . $value['cus_pemilik'] : '';
                $opt .= '<option value="' . $value['cus_id'] . '" data-harga="' . $value['cus_harga'] . '">' . ucfirst($value['cus_nama']) . $pemilik . '</option>';
            }
        }
        return $opt;
    }

    function optgudang()
    {
        $data = $this->Umodel->get_data('m_gudang', array('gdg_isjual' => 1), 'gdg_nama');
        $opt = '';
        foreach ($data->result_array() as $value) {
            $opt .= '<option value="' . $value['gdg_id'] . '">' . $value['gdg_nama'] . '</option>';
        }
        return $opt;
    }

    function optkategori()
    {
        $data = $this->Umodel->li_data('m_produk_kategori');
        $opt = '<option value="0">Semua Kategori Produk</option>';
        foreach ($data->result_array() as $value) {
            $opt .= '<option value="' . $value['ktg_id'] . '">' . ucfirst($value['ktg_nama']) . '</option>';
        }
        return $opt;
    }

    function cbrek_()
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

            function filtertrop_()
            {
                $arr['tbody'] = $this->trop_();
                echo json_encode($arr);
            }

            function trop_()
            {
                $data = $this->Umodel->li_data('m_armada');
                ob_start();
                $allowedlevel = array(1);
                ?>
        <tr>
            <td>
                <input type="hidden" class="form-control" name="idarmada" value="">
                <div class="row">
                    <div class="col-md-4 col-xs-4"><input type="text" class="form-control" value="Armada" name="armada[]" readonly=""></div>
                    <div class="col-md-8 col-xs-8">
                        <select class="form-control" name="armada[]" id="armada">
                            <option selected="" value="">-Kosong-</option>
                            <?php
                                    foreach ($data->result_array() as $val) {
                                        ?>
                                <option value="<?= $val['arm_jenis'] . ' ' . $val['arm_nopol'] ?>"><?= $val['arm_jenis'] . ' [' . $val['arm_nopol'] . ']' ?></option>
                            <?php
                                    }
                                    ?>
                        </select>
                    </div>
                </div>
            </td>
            <td>
                <?php if (in_array($this->session->userdata('log_level'), $allowedlevel)) { ?>
                    <input type="text" class="form-control" name="biayaarmada" value="" autocomplete="off">
                <?php } else { ?>
                    <input type="hidden" class="form-control" name="biayaarmada" value="">
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="hidden" class="form-control" name="idsopir" value="">
                <div class="row">
                    <div class="col-md-4 col-xs-4"><input type="text" class="form-control" value="Sopir" name="sopir[]" readonly=""></div>
                    <div class="col-md-8 col-xs-8"><input type="text" class="form-control" value="" name="sopir[]" id="sopir"></div>
                </div>
            </td>
            <td>
                <?php if (in_array($this->session->userdata('log_level'), $allowedlevel)) { ?>
                    <input type="text" class="form-control" name="biayasopir" value="" autocomplete="off">
                <?php } else { ?>
                    <input type="hidden" class="form-control" name="biayasopir" value="">
                <?php } ?>
            </td>
        </tr>
    <?php
            $tr = ob_get_contents();
            ob_clean();
            return $tr;
        }

        function ssplist_($kategori = 0)
        {
            $this->load->model('Mastermodel');
            $gudang = $this->session->userdata('log_gudang');
            $aColumns = array('prd_kode', 'prd_nama', 'stok', 'stk_kadaluarsa', 'prd_satuan', 'prd_id');
            $sIndexColumn = $aColumns[2];
            $vColumns = array('prd_nama');

            // paging
            $sLimit = "";
            if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
                $sLimit = "LIMIT " . $_GET['iDisplayStart'] . ", " .
                    $_GET['iDisplayLength'];
            }
            $numbering = $_GET['iDisplayStart'];

            // ordering
            if (isset($_GET['iSortCol_0'])) {
                $sOrder = "ORDER BY ";

                for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                    if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                        $sOrder .= $aColumns[intval($_GET['iSortCol_' . $i])] . " " . $_GET['sSortDir_' . $i] . ", ";
                    }
                }
                $sOrder = substr_replace($sOrder, "", -2);
                if ($sOrder == "ORDER BY") {
                    $sOrder = "";
                }
            }

            // filtering
            $sWhere = "";
            if ($_GET['sSearch'] != "") {
                $sWhere = "WHERE (";
                for ($i = 0; $i < count($vColumns); $i++) {
                    $sWhere .= $vColumns[$i] . " LIKE '%" . $_GET['sSearch'] . "%' OR ";
                }

                $sWhere = substr_replace($sWhere, "", -3);
                $sWhere .= ')';
            }

            // individual column filtering
            for ($i = 0; $i < count($vColumns); $i++) {
                if ($_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != "") {
                    if ($sWhere == "") {
                        $sWhere = "WHERE ";
                    } else {
                        $sWhere .= " AND ";
                    }
                    $sWhere .= $vColumns[$i] . " LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
                }
            }
            $rResult = $this->Mastermodel->ssppenjualan($vColumns, $sWhere, $sOrder, $sLimit, $gudang, $kategori);
            $iFilteredTotal = 10;
            $rResultTotal = $this->Mastermodel->ssppenjualan_total($sIndexColumn, $sWhere, $sOrder, $gudang, $kategori);
            $iTotal = $rResultTotal;

            $iFilteredTotal = $iTotal;
            $output = array(
                "sEcho" => intval($_GET['sEcho']),
                "iTotalRecords" => $iTotal,
                "iTotalDisplayRecords" => $iFilteredTotal,
                "aaData" => array()
            );

            $no = (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') ? $_GET['iDisplayStart'] : 0;
            foreach ($rResult as $aRow) {
                $kode = ($aRow['prd_kode'] == '') ? '' : '[' . $aRow['prd_kode'] . '] ';
                $aksi = '<a id="btn-add" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="left" title="Tambah Barang" data-id="' . $aRow['prd_id'] . '" data-nama="' . $aRow['prd_nama'] . '" data-satuan="' . $aRow['prd_satuan'] . '" data-kode="' . $aRow['prd_kode'] . '" data-hb="' . $aRow['prd_hargabeli'] . '" data-hargaecer="' . $aRow['prd_hargaecer'] . '" data-hargagrosir="' . $aRow['prd_hargagrosir'] . '" data-hargamember="' . $aRow['prd_hargamember'] . '" data-hargakhusus="' . $aRow['prd_hargakhusus'] . '" data-stok="' . $aRow['stok'] . '" data-kadaluarsa="' . $aRow['stk_kadaluarsa'] . '" data-nobatch="' . $aRow['stk_nobatch'] . '"><i class="fa fa-plus"></i></a>';
                $row = array(($no + 1), $kode . $aRow['prd_nama'], $this->ascfunc->nf_($aRow['stok']), $aRow['prd_satuan'], $this->ascfunc->nf_($aRow['prd_hargaecer']), $aRow['stk_kadaluarsa'], $aksi);
                $output['aaData'][] = $row;
                $no++;
            }

            echo json_encode($output);
        }

        function save_()
        {
            $tgl = strtotime(str_replace('/', '-', $this->input->post('tanggal')));
            $getsys = $this->ascfunc->sys_();
            $getnonota = 0;
            if (date('Y') == $getsys['counter_tahun']) {
                $getnonota = $getsys['counter_nota'] + 1;
            } else {
                $getnonota = 1;
            }
            $nonota = sprintf('%07d', $getnonota);

            $data = array(
                'nota_id' => $this->input->post('nid'),
                'nota_no' => $nonota,
                'nota_asal' => $this->session->userdata('log_gudang'),
                'nota_tujuan' => $this->input->post('customer'),
                'nota_tanggal' => date('Y-m-d', $tgl),
                'nota_jam' => date('H:i:s', $tgl),
                'nota_ppn' => ($this->input->post('ppn') == '') ? 0 : $this->input->post('ppn'),
                'nota_iskredit' => $this->input->post('kredit'),
                'nota_diskon' => abs($this->ascfunc->cnf_($this->input->post('diskon'))),
                'nota_cb_id' => $this->session->userdata('log_id'),
                'nota_cb' => $this->session->userdata('log_nama'),
                'nota_pj' => $this->session->userdata('log_nama'),
                'nota_isverifikasi' => 1,
                'nota_jenis' => 'JUAL'
            );
            $id = $this->input->post('id');
            $qty = $this->input->post('qty');
            $hb = $this->input->post('hb');
            $hj = $this->input->post('hj');
            $kadaluarsa = $this->input->post('kadaluarsa');
            $nobatch = $this->input->post('nobatch');

            $total = 0;
            $detail = array();
            foreach ($id as $key => $val) {
                if ($qty[$key] != '') {
                    $temp = array(
                        'id' => $val,
                        'jumlah' => $qty[$key],
                        'hb' => $hb[$key],
                        'hj' => $hj[$key],
                        'tes_kadaluarsa' => $kadaluarsa[$key],
                        'tes_nobatch' => $nobatch[$key]
                    );
                    $total += $hj[$key] * $qty[$key];
                    array_push($detail, $temp);
                }
            }
            $total = $total - $data['nota_diskon'];
            $grandtotal = ($data['nota_ppn'] > 0) ? $total + ($total * $data['nota_ppn'] / 100) : $total;
            $data['nota_total'] = ($data['nota_iskredit']) ? 0 : $grandtotal;
            $operasional = array();
            if (!$this->input->post('noarmada')) {
                $operasional[] = array(
                    'id' => $this->input->post('idarmada'),
                    'op' => implode(' ', $this->input->post('armada')),
                    'biaya' => abs($this->input->post('biayaarmada'))
                );
                $operasional[] = array(
                    'id' => $this->input->post('idsopir'),
                    'op' => implode(' ', $this->input->post('sopir')),
                    'biaya' => abs($this->input->post('biayasopir'))
                );
            }
            $op = $this->input->post('op');
            if (count($op) > 0) {
                $idop = $this->input->post('idop');
                $biaya = $this->input->post('biaya');
                foreach ($op as $key => $val) {
                    if ($val != '') {
                        $operasional[] = array(
                            'id' => $idop[$key],
                            'op' => $val,
                            'biaya' => abs($biaya[$key])
                        );
                    }
                }
            }
            $dp = 0;
            $pembayaran = array();
            $dp = $this->ascfunc->cnf_($this->input->post('dp'));
            if ($data['nota_iskredit']) {
                $pembayaran = array(
                    'tanggal' => date('Y-m-d', $tgl),
                    'jam' => date('H:i:s', $tgl),
                    'nominal' => abs($dp)
                );
            }
            $q = $this->Transaksimodel->sv_penjualan($data, $detail, $operasional, $pembayaran, $this->input->post('rekening'));
            $this->Umodel->sv_data('m_sistem', array('sis_deskripsi' => $getnonota), true, array('sis_kode' => 'counter_nota'));
            $this->Umodel->sv_data('m_sistem', array('sis_deskripsi' => date('Y')), true, array('sis_kode' => 'counter_tahun'));
            if ($q != FALSE) {
                $arr['thermal'] = 0;
                if (abs($this->input->post('customer')) == 0) {
                    $arr['thermal'] = 1;
                    $this->cetakthermal($q, $dp);
                }
                $arr['id'] = $q;
                $arr['ind'] = 1;
                $arr['msg'] = 'Data telah tersimpan';
            } else {
                $arr['ind'] = 0;
                $arr['msg'] = 'Terjadi kesalahan, Data gagal disimpan';
            }
            echo json_encode($arr);
        }

        function cetak($id)
        {
            $output['bagian'] = '';
            $output['data'] = $this->Transaksimodel->get_ntpj($id);
            $output['info'] = $this->ascfunc->info_();
            $output['rek'] = $this->Umodel->li_data('m_rekening')->result_array();
            $cproduk = count($output['data']['detail']);
            $output['halnota'] = ceil($cproduk / 10);
            $output['idcetak'] = $id;
            // var_dump($output['rek']);
            // die;
            $this->load->view('laporan/cetak', $output);
        }
        function cetak_extended($id)
        {
            $output['bagian'] = $this->input->get('p');
            $output['data'] = $this->Transaksimodel->get_ntpj($id);
            $output['info'] = $this->ascfunc->info_();
            $output['rek'] = $this->Umodel->li_data('m_rekening')->result_array();
            $cproduk = count($output['data']['detail']);
            $output['halnota'] = ceil($cproduk / 10);

            // var_dump($output['rek']);
            // die;
            $this->load->view('laporan/cetak', $output);
        }

        function cetaksj($id)
        {
            $output['data'] = $this->Transaksimodel->get_ntpj($id);
            $output['info'] = $this->ascfunc->info_();
            $this->load->view('laporan/cetaksj', $output);
        }

        function cetakthermal($id, $bayar)
        {
            $info = $this->ascfunc->info_();
            $data = $this->Transaksimodel->get_ntpj($id);
            $customer = ($data['nota_tujuan'] > 0) ? $data['cus_nama'] : 'Umum';
            try {
                $connector = new WindowsPrintConnector("EPSON-80");
                $pos = new Printer($connector);
                $pos->setEmphasis(true);
                $pos->setTextSize(2, 2);
                $pos->setJustification(Printer::JUSTIFY_CENTER);
                $pos->setFont(Printer::FONT_B);
                $pos->text($info['perusahaan'] . "\n");
                $pos->setEmphasis(false);
                $pos->setTextSize(1, 1);
                $pos->text($info['alamat'] . '-' . $info['kecamatan'] . "\n");
                $pos->text($info['kabupaten'] . "\n");
                $pos->text('Telp. ' . $info['hp'] . ', ' . $info['telepon'] . "\n");
                $pos->setFont();
                $pos->feed();
                $pos->setJustification(Printer::JUSTIFY_LEFT);
                $pos->text("Tanggal  : " . date('d/m/Y H:i', strtotime($data['nota_tanggal'] . ' ' . $data['nota_jam'])) . "\n");
                $pos->text("Customer : " . $customer . "\n");
                $pos->text("Kasir    : " . $data['nota_cb'] . "\n");
                $pos->feed();
                $pos->text($this->p_headerpesanan() . "\n");
                $pos->text("_________________________________\n");
                $pos->setJustification();
                $subtotal = 0;
                foreach ($data['detail'] as $val) {
                    $amount = $val['dtn_jumlah'] * $val['dtn_hargajual'];
                    $pos->text($val['prd_nama'] . "\n");
                    $pos->text($this->p_pesanan($val['dtn_jumlah'], $val['prd_satuan'], $val['dtn_hargajual'], $amount) . "\n");
                    $subtotal += $amount;
                }
                $pos->setEmphasis(true);
                $pos->text("_________________________________\n");
                $pos->text($this->p_onerow("Sub Total", "Rp. " . number_format($subtotal, 0, ',', '.'), $this->len) . "\n");
                $t_discount = $data['nota_diskon'];
                $pos->text($this->p_onerow("Diskon", "Rp. " . number_format($t_discount, 0, ',', '.'), $this->len) . "\n");
                $t_tax = 0;
                $pos->text("_________________________________\n");
                $total = $subtotal - $t_discount + $t_tax;
                $pos->text($this->p_onerow("Grand Total", "Rp. " . number_format($total, 0, ',', '.'), $this->len) . "\n");
                $pos->text($this->p_onerow("Bayar", "Rp. " . number_format(abs($bayar), 0, ',', '.'), $this->len) . "\n");
                $pos->text($this->p_onerow("Kembali", "Rp. " . number_format(abs($bayar) - $total, 0, ',', '.'), $this->len) . "\n");
                $pos->feed();
                $pos->setEmphasis(false);
                $pos->setJustification(Printer::JUSTIFY_CENTER);
                $pos->text('"Terima kasih telah berbelanja"');
                $pos->setJustification(Printer::JUSTIFY_LEFT);
                $pos->feed(7);
                $pos->close();
            } catch (Exception $e) {
                $arr['ind'] = 0;
                $arr['msg'] = 'Terjadi kesalahan pada printer, silahkan cek printer anda.';
                echo json_encode($arr);
                exit();
            }
        }

        private function p_headerpesanan()
        {
            $text = "Nama Barang\n";
            $text .= str_pad('Qty', 6);
            $text .= str_pad('Stn', 5);
            $text .= str_pad('Harga', 10);
            $text .= str_pad('Total', 12, ' ', STR_PAD_LEFT);
            return $text;
        }

        private function p_pesanan($qty, $satuan, $harga, $total)
        {
            $text = '';
            $text .= str_pad($qty, 6);
            $text .= str_pad($satuan, 5);
            $harga_ = $this->ascfunc->nf_($harga);
            $text .= str_pad($harga_, 10);
            $text .= str_pad($this->ascfunc->nf_($total), 12, ' ', STR_PAD_LEFT);
            return $text;
        }

        private function p_onerow($tleft, $tright, $len)
        {
            $ln = $len - strlen($tleft . $tright);
            if ($ln < 0) {
                return substr($tleft . $tright, 0, $len);
            }
            $nbsp = "";
            for ($i = 0; $i < $ln; $i++) {
                $nbsp .= " ";
            }
            return $tleft . $nbsp . $tright;
        }

        function ssplistnota_($gudang, $bulan = '', $tahun = '')
        {
            $level = $this->session->userdata('log_level');
            $toplevel = array(1, 2);
            $isadmin = (in_array($level, $toplevel)) ? TRUE : FALSE;
            $aColumns = array('nota_tanggal', 'cus_nama', 'cus_nama', 'nota_id');
            $sIndexColumn = $aColumns[0];
            $vColumns = array('cus_nama');

            // paging
            $sLimit = "";
            if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
                $sLimit = "LIMIT " . $_GET['iDisplayStart'] . ", " .
                    $_GET['iDisplayLength'];
            }
            $numbering = $_GET['iDisplayStart'];

            // filtering
            $sWhere = "";
            if ($_GET['sSearch'] != "") {
                $sWhere = "WHERE (";
                for ($i = 0; $i < count($vColumns); $i++) {
                    $sWhere .= $vColumns[$i] . " LIKE '%" . $_GET['sSearch'] . "%' OR ";
                }

                $sWhere = substr_replace($sWhere, "", -3);
                $sWhere .= ')';
            }

            // individual column filtering
            for ($i = 0; $i < count($vColumns); $i++) {
                if ($_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != "") {
                    if ($sWhere == "") {
                        $sWhere = "WHERE ";
                    } else {
                        $sWhere .= " AND ";
                    }
                    $sWhere .= $vColumns[$i] . " LIKE '%" . $_GET['sSearch_' . $i] . "%' ";
                }
            }
            $rResult = $this->Transaksimodel->sspntpj($vColumns, $sWhere, $sLimit, $bulan, $tahun, $gudang);
            $iFilteredTotal = 10;
            $rResultTotal = $this->Transaksimodel->sspntpj_total($sIndexColumn, $sWhere, $bulan, $tahun, $gudang);
            $iTotal = $rResultTotal;

            $iFilteredTotal = $iTotal;
            $output = array(
                "sEcho" => intval($_GET['sEcho']),
                "iTotalRecords" => $iTotal,
                "iTotalDisplayRecords" => $iFilteredTotal,
                "aaData" => array()
            );

            foreach ($rResult as $aRow) {
                $tanggal = date('d/m/Y', strtotime($aRow['nota_tanggal'])) . ' ' . date('H:i', strtotime($aRow['nota_jam']));
                $aksi = ($aRow['nota_tujuan'] == 0) ? '' : '<a id="btn-print" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="left" title="Print Nota" href="' . base_url('penjualan/cetak/' . $aRow['nota_id']) . '" target="_blank"><i class="fa fa-print"></i></a> <a id="btn-print" class="btn btn-xs btn-warning" data-toggle="tooltip" data-placement="left" title="Print Surat Jalan" href="' . base_url('penjualan/cetaksj/' . $aRow['nota_id']) . '" target="_blank"><i class="fa fa-truck"></i></a> ';
                $aksi .= ($level != 5) ? '<a id="btn-select" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="left" title="Detail Nota" data-id="' . $aRow['nota_id'] . '"><i class="fa fa-arrow-right"></i></a> ' : '';
                $aksi .= ($isadmin) ? '<a id="btn-delete" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="left" title="Hapus Nota" data-id="' . $aRow['nota_id'] . '"><i class="fa fa-trash"></i></a>' : '';
                $customer = ($aRow['nota_tujuan'] == 0) ? 'Umum' : $aRow['cus_nama'];
                $customer .= ($aRow['nota_tujuan'] > 0 && $aRow['cus_iskios']) ? ' - ' . $aRow['cus_pemilik'] : '';
                $row = array($tanggal, $customer, $aRow['detail'], $aksi);
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
        }

        function getnota_($id)
        {
            $data = $this->Transaksimodel->get_ntpj($id);
            $arr['content'] = $this->nota_($data);
            echo json_encode($arr);
        }

        private function nota_old($data)
        {
            $info = $this->ascfunc->info_();
            ob_start();
            ?>
        <div class="col-md-7 col-xs-7">
            <h4 style="margin: 0;font-weight: bold;"><?= $info['perusahaan'] ?></h4>
            <p style="margin-bottom: 0"><?= $info['alamat'] . '-' . $info['kecamatan'] ?></p>
            <p style="margin-bottom: 0"><?= $info['hp'] . ', ' . $info['telepon'] ?></p>
            <p><?= $info['kabupaten'] ?></p>

            <table style="margin-top: 20px; width: 100%;">
                <tbody>
                    <tr>
                        <td style="width: 20%">Tanggal</td>
                        <td style="width: 80%">: <?= date('d/m/Y', strtotime($data['nota_tanggal'])) ?></td>
                    </tr>
                    <tr>
                        <td>Jt.Tempo</td>
                        <td>: </td>
                    </tr>
                    <tr>
                        <td>Gudang</td>
                        <td>: <?= $data['asal'] ?></td>
                    </tr>
                </tbody>
            </table>

        </div>
        <div class="col-md-5 col-xs-5">
            <h3 style="margin: 0;"><span style="font-style: italic">FAKTUR PENJUALAN</span> <span class="pull-right" style="font-weight: bold;"><?= 'FJ-' . $data['nota_id'] ?></span></h3>
            <br>
            <p style="margin-bottom: 0; margin-top: 35px; font-weight: bold">Customer</p>
            <?php
                    if ($data['nota_tujuan'] > 0) {
                        $pemilik = ($data['cus_iskios']) ? ' - ' . $data['cus_pemilik'] : '';
                        ?>
                <p style="margin-bottom: 0;"><?= $data['cus_nama'] . $pemilik; ?></p>
                <p style="margin-bottom: 0"><?= $data['cus_alamat'] ?></p>
                <p><?= $data['cus_telp'] ?></p>
            <?php } else { ?>
                <p>Umum</p>
            <?php } ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-12 col-xs-12">
            <table class="table table-responsive" style="margin-bottom: 20px">
                <thead style="border-top: 1px solid #000;">
                    <tr>
                        <th style="width: 10%; border-bottom: 1px solid #000;">Kode</th>
                        <th style="width: 35%; border-bottom: 1px solid #000;">Nama Barang</th>
                        <th class="text-right" style="width: 5%; border-bottom: 1px solid #000;">Qty</th>
                        <th style="width: 5%; border-bottom: 1px solid #000;">Satuan</th>
                        <th class="text-right" style="width: 15%; border-bottom: 1px solid #000;">@Harga</th>
                        <th class="text-right" style="width: 20%; border-bottom: 1px solid #000;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                            foreach ($data['detail'] as $val) {
                                $jumlah = ($val['dtn_jumlah'] == '') ? '' : $val['dtn_jumlah'] * $val['dtn_hargajual'];
                                ?>
                        <tr>
                            <td><?= $val['prd_kode'] ?></td>
                            <td><?= $val['prd_nama'] ?></td>
                            <td class="text-right"><?= $this->ascfunc->nf_($val['dtn_jumlah']) ?></td>
                            <td><?= $val['prd_satuan'] ?></td>
                            <td class="text-right"><?= $this->ascfunc->nf_($val['dtn_hargajual']) ?></td>
                            <td class="text-right"><?= $this->ascfunc->nf_($jumlah) ?></td>
                        </tr>
                    <?php
                            }
                            ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-right" colspan="5" style="border-top: 1px solid #000;">TOTAL = Rp.</td>
                        <td class="text-right" style="border-top: 1px solid #000;"><?= $this->ascfunc->nf_($data['total']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-right" colspan="5" style="border-top: 1px solid #000;">Diskon = Rp.</td>
                        <td class="text-right" style="border-top: 1px solid #000;"><?= $this->ascfunc->nf_($data['nota_diskon']) ?></td>
                    </tr>
                    <!--                    <tr>
                        <td class="text-right" colspan="5">PPN(<?= $data['nota_ppn'] . '%' ?>) = Rp.</td>
                        <td class="text-right"><?= $this->ascfunc->nf_($data['ppn']) ?></td>
                    </tr>                    -->
                    <tr>
                        <td class="text-right" colspan="5" style="border-top: 1px solid #000;">GRAND TOTAL = Rp.</td>
                        <td class="text-right" style="border-top: 1px solid #000;"><?= $this->ascfunc->nf_($data['grandtotal']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" style="border-top: 1px solid #000; font-size: 17px;"><?= 'Terbilang : <span style="font-style: italic;">' . $data['terbilang'] . ' Rupiah</span>'; ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-4 col-xs-4 text-center">
            Dibuat Oleh<br><br><br><br>
            (<?= $data['nota_cb'] ?>)
        </div>
        <div class="col-md-4 col-xs-4 text-center">
            Penerima<br><br><br><br>
            <?= ($data['nota_tujuan'] > 0) ? $data['cus_nama'] . '<br>' . substr($pemilik, 3) : ''; ?>
        </div>
        <div class="col-md-4 col-xs-4 text-center">
            Hormat Kami<br><br><br><br>
            <?= $data['nota_pj'] ?>
        </div>
    <?php
            $content = ob_get_contents();
            ob_clean();
            return $content;
        }
        private function nota_($data)
        {
            $rek = $this->Umodel->li_data('m_rekening')->result_array();
            $info = $this->ascfunc->info_();
            ob_start();
            ?>
		<div style="font-size:11px">	
        <div class="col-md-8 col-xs-8" style="line-height:1.1;">

            <img style="float:left; margin-right:5px;margin-bottom:2px" height="70px" width="70px" src="<?= base_url() ?>/assets/logoonly.png" alt="">
            <h4 style="margin: 0;font-weight: bold;"><?= $info['perusahaan'] ?></h4>

				<p style="margin-bottom: 0"><?= $info['deskripsi'] ?></p>
                <p style="margin-bottom: 0"><?= $info['alamat'] . ', ' . $info['kecamatan'] . ', ' . $info['kabupaten'] ?></p>
                <p style="margin-bottom: 0"><?= 'Telp. ' . $info['hp'] . ', ' . $info['telepon'] ?> </p>
				<p style="margin-bottom: 0"><?= 'Email: '?> tokotanisamarinda@gmail.com,</p>


            <table style="margin-top: 0px; margin-bottom:5px;width: 100%;">
                <tbody>
						<tr>
							<td style="width: 10%">Dicetak</td>
                            <td class="text-center" style="width: 2%">:</td>
                            <td style="width: 88%"><?= $data['nota_cb'] . ' - ' . date('d/m/Y', strtotime($data['nota_tanggal'])) . ' ' . $data['nota_jam'] ?></td>
                        </tr>
                        <tr>
                            <td style="width: 15%">No Nota</td>
                            <td class="text-center" style="width: 2%">:</td>
                            <td style="width: 83%"><?= $data['nota_no'] . $halaman ?></td>
                        </tr>
						<tr>
                            <td style="width: 20%">Tgl. Jatuh Tempo</td>
                            <td class="text-center" style="width: 2%">:</td>
                            <td style="width: 78%"><?= date('d/m/Y', strtotime('+30 days', strtotime($data['nota_tanggal']))); ?></td>
							
                        </tr>
                    <!-- <tr>
                        <td>Jt.Tempo</td>
                        <td>: </td>
                    </tr> -->
                    <!-- <tr>
                        <td>Gudang</td>
                        <td>: <?= $data['asal'] ?></td>
                    </tr> -->
                </tbody>
            </table>

        </div>
        <div class="col-md-4 col-xs-4" style="line-height:1.1;">
            <!-- <h3 style="margin: 0;">
                <span style="font-style: italic">FAKTUR PENJUALAN</span>
                <span class="pull-right" style="font-weight: bold;"><?= 'FJ-' . $data['nota_id'] ?></span>
            </h3> -->

            <p style="margin-bottom: 0;"><?= $info['kabupaten'] . ', ' . date('d-m-Y', strtotime($data['nota_tanggal'])) ?></p>

            <p style="margin-bottom: 0; margin-top:25px;">Kepada Yth.</p>
            <?php
                    if ($data['nota_tujuan'] > 0) {
                        $pemilik = ($data['cus_iskios']) ? ' - ' . $data['cus_pemilik'] : '';
                        ?>
                <p style="margin-bottom: 0"><?= $data['cus_nama'] . $pemilik; ?></p>
                <p style="margin-bottom: 0"><?= $data['cus_alamat'] ?></p>
                <p style="margin-bottom: 5px"><?= $data['cus_telp'] ?></p>
            <?php } else { ?>
                <p>Customer Umum</p>
            <?php } ?>
        </div>
        <!-- <div class="clearfix"></div> -->
        <div class="col-md-12 col-xs-12">
            <?php
                    $no = 1;
                    ?>
            <table class="" style="margin-bottom: 0px;line-height:1.1">
                <thead style="border-top: 1px solid #000;">
                    <tr style="margin:0px">
                        <th style="width: 0%; border-bottom: 1px solid #000; padding:0;">NO</th>
                        <th class="text-center" style="width: 3%; border-bottom: 1px solid #000;padding:0;">CEK</th>
                        <!-- <th style="width: 10%; border-bottom: 1px solid #000;">Kode</th> -->
                        <th class="text-center" style="width: 8%; border-bottom: 1px solid #000;padding:0;">QTY</th>
                        <th class="text-center" style="width: 52%; border-bottom: 1px solid #000;padding:0;">NAMA BARANG</th>
                        <!-- <th style="width: 5%; border-bottom: 1px solid #000;">Satuan</th> -->
                        <th class="text-center" style="width: 15%; border-bottom: 1px solid #000;padding:0;">HARGA @</th>
                        <th class="text-center" style="width: 20%; border-bottom: 1px solid #000;padding:0;">SUBTOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                            foreach ($data['detail'] as $val) {
                                $jumlah = ($val['dtn_jumlah'] == '') ? '' : $val['dtn_jumlah'] * $val['dtn_hargajual'];
                                ?>
                        <tr>
                            <td style="padding:0;"><?= $no ?></td>
                            <td class="text-center" style="padding:0;">
                                <input type="checkbox">
                            </td>
                            <!-- <td><?= $val['prd_kode'] ?></td> -->
                            <td style="padding:0;"><?= $this->ascfunc->nf_($val['dtn_jumlah']) . ' ' . $val['prd_satuan'] ?></td>
                            <td style="padding:0;"><?= $val['prd_nama'] ?></td>
                            <td style="padding:0;" class="text-right">Rp. <?= $this->ascfunc->nf_($val['dtn_hargajual']) ?></td>
                            <td style="padding:0;" class="text-right">Rp. <?= $this->ascfunc->nf_($jumlah) ?></td>
                        </tr>
                    <?php
                                $no++;
                            }
                            ?>
                </tbody>
                <tfoot style="line-height:1.3">
                    <tr style="border-top: 1px solid #000;">
                        <td colspan="4" style="padding:0;"><?= "Terbilang : <span>\"# " . $data['terbilang'] . " Rupiah #\"</span>"; ?></td>
                        <td class="text-right" style="padding:0;">TOTAL</td>
                        <td class="text-right" style="padding:0;">Rp. <?= $this->ascfunc->nf_($data['total']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-right" colspan="5" style="padding:0;">Diskon</td>
                        <td class="text-right" style="padding:0;">Rp. <?= $this->ascfunc->nf_($data['nota_diskon']) ?></td>
                    </tr>
                    <!--                        <tr>
                            <td class="text-right" colspan="5">PPN(<?= $data['nota_ppn'] . '%' ?>) : Rp.</td>
                            <td class="text-right"><?= $this->ascfunc->nf_($data['ppn']) ?></td>
                        </tr>                    -->
                    <tr>
                        <td colspan="4"></td>
                        <td class="text-right" style="padding:0;">GRAND TOTAL</td>
                        <td class="text-right" style="border-top: 1px solid #000;padding:0;">Rp. <?= $this->ascfunc->nf_($data['grandtotal']) ?></td>
                    </tr>
                    <?php if ($data['nota_iskredit']) { ?>
                        <tr>
                            <td colspan="5" class="text-right" style="padding:0;">Down Payment</td>
                            <td class="text-right" style="padding:0;">Rp.<?= $data['dp'] ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-right" style="padding:0;">Sisa Piutang</td>
                            <td class="text-right" style="border-top: 1px solid #000;padding:0;">Rp. <?= $data['sisa'] ?></td>
                        </tr>
                    <?php } ?>
                </tfoot>
            </table>
        </div>
        <!-- <div class="clearfix"></div> -->
        <div class="col-md-12 col-xs-12" style=" margin-top:-30px;">
            <div class="col-md-2 col-xs-2 text-center">
                Penerima<br><br><br>
                <?= ($data['nota_tujuan'] > 0) ? $data['cus_nama'] . '<br>' . substr($pemilik, 3) : ''; ?>
            </div>
            <!-- <div class="col-md-4 col-xs-4 text-center">
            Dibuat Oleh<br><br><br><br>
            <?= $data['nota_cb'] ?>
        </div> -->
            <!-- <div class="col-md-4 col-xs-4 text-center">
            Hormat Kami<br><br><br><br>
            <?= $data['nota_pj'] ?>
        </div> -->
            <div class="col-md-2 col-xs-2 text-center">
                Hormat Kami<br><br><br>
                <?//= $data['nota_cb'] ?>
				RANA RAHMI
            </div>
            <div class="col-md-4 col-xs-4 text-center">
                    <p align="left"><?= $info['perusahaan'] ?><br>
                    <?//= "No. Rek : " . $rek[0]['rek_nomor'] ?>
					Bank Mandiri: 148500408888<br>
					Bank Kaltim: 129183891<br>
					Bank BCA: 2700423410</p>
            </div>
            <div class="col-md-4"></div>
			 <div class="col-md-12 col-xs-12" style=" margin-top:10px">
			 *) BARANG YANG SUDAH DIBELI TIDAK DAPAT DI TUKAR/DIKEMBALIKAN.
			 </div>
		 </div>
	</div>
    <?php
            $content = ob_get_contents();
            ob_clean();
            return $content;
        }

        function delete_($id)
        {
            $q = $this->Transaksimodel->del_pj($id);
            if ($q) {
                $arr['ind'] = 1;
                $arr['msg'] = 'Data berhasil dihapus';
            } else {
                $arr['ind'] = 0;
                $arr['msg'] = 'Terjadi kesalahan, Data gagal dihapus';
            }
            echo json_encode($arr);
        }
    }
