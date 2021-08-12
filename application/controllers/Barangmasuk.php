<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barangmasuk extends CI_Controller
{

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
        $allowedlevel = array(1);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('barangmasuk-menu');
        $header['css'] = array('assets/plugin/datetimepicker/css/bootstrap-datetimepicker.min.css', 'assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/js/bootstrap-typeahead.js', 'assets/plugin/moment/moment.min.js', 'assets/plugin/datetimepicker/js/bootstrap-datetimepicker.min.js', 'assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $sys = $this->ascfunc->sys_();
        $header['ppn'] = $sys['ppn'];
        $body = array(
            'optsupplier' => $this->optsupplier(),
            'optgudang' => $this->optgudang(),
            'cbrek' => $this->cbrek_(),
            'trop' => $this->trop_()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('transaksi/barangmasuk', $body);
        $this->load->view('templates/footer');
    }

    public function keu()
    {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(3);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('barangmasuk-menu');
        $header['css'] = array('assets/plugin/datetimepicker/css/bootstrap-datetimepicker.min.css', 'assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/js/bootstrap-typeahead.js', 'assets/plugin/moment/moment.min.js', 'assets/plugin/datetimepicker/js/bootstrap-datetimepicker.min.js', 'assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $sys = $this->ascfunc->sys_();
        $header['ppn'] = $sys['ppn'];
        $body = array(
            'optsupplier' => $this->optsupplier(),
            'optgudang' => $this->optgudang(),
            'cbrek' => $this->cbrek_(),
            'trop' => $this->trop_()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('transaksi/barangmasukkeu', $body);
        $this->load->view('templates/footer');
    }

    function optsupplier()
    {
        $data = $this->Umodel->li_data('m_supplier', 'sup_nama');
        $opt = '<option value="0">-Pilih Supplier-</option>';
        foreach ($data->result_array() as $value) {
            $opt .= '<option value="' . $value['sup_id'] . '">' . ucfirst($value['sup_nama']) . '</option>';
        }
        return $opt;
    }

    function optgudang()
    {
        $data = $this->Umodel->get_data('m_gudang', array('gdg_isjual' => 0), 'gdg_nama');
        $opt = '<option value="0">-Pilih Lokasi-</option>';
        foreach ($data->result_array() as $value) {
            $opt .= '<option value="' . $value['gdg_id'] . '">' . $value['gdg_nama'] . '</option>';
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

            function cari_()
            {
                $search = strtolower($this->input->post('w'));
                $sql = $this->Umodel->find_data('m_penanggung_jawab', array('LOWER(pj_nama)' => $search));
                $dt = array();
                foreach ($sql as $val) {
                    $data['content'] = $val['pj_nama'];
                    array_push($dt, $data);
                }
                echo json_encode($dt);
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

        function ssplist_($supplier = '0', $gudang = '0')
        {
            $this->load->model('Mastermodel');
            $dtgudang = $this->Umodel->get_data('m_gudang', array('gdg_id' => $gudang))->row_array();
            if ($dtgudang['gdg_produk_kategori'] != '') {
                $kategori = implode(',', json_decode($dtgudang['gdg_produk_kategori'], TRUE));
            } else {
                $kategori = 0;
            }
            $aColumns = array('prd_kode', 'prd_nama', 'prd_id');
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
            $rResult = $this->Mastermodel->sspbarangmasuk($vColumns, $sWhere, $sOrder, $sLimit, $supplier, $kategori);
            $iFilteredTotal = 10;
            $rResultTotal = $this->Mastermodel->sspbarangmasuk_total($sIndexColumn, $sWhere, $sOrder, $supplier, $kategori);
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
                $aksi = '<a id="btn-add" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="left" title="Tambah Barang" data-id="' . $aRow['prd_id'] . '" data-nama="' . $aRow['prd_nama'] . '" data-satuan="' . $aRow['prd_satuan'] . '" data-kode="' . $aRow['prd_kode'] . '" data-hb="' . $aRow['prd_hargabeli'] . '"><i class="fa fa-plus"></i></a>';
                $row = array(($no + 1), $kode . $aRow['prd_nama'], $aksi);
                $output['aaData'][] = $row;
                $no++;
            }

            echo json_encode($output);
        }

        function save_()
        {
            $tgl = strtotime(str_replace('/', '-', $this->input->post('tanggal')));
            $data = array(
                'nota_id' => $this->input->post('nid'),
                'nota_asal' => $this->input->post('supplier'),
                'nota_tujuan' => $this->input->post('gudang'),
                'nota_tanggal' => date('Y-m-d', $tgl),
                'nota_jam' => date('H:i:s', $tgl),
                'nota_ppn' => ($this->input->post('ppn') == '') ? 0 : $this->input->post('ppn'),
                'nota_iskredit' => $this->input->post('kredit'),
                'nota_cb_id' => $this->session->userdata('log_id'),
                'nota_cb' => $this->session->userdata('log_nama'),
                'nota_pj' => $this->input->post('pj'),
                'nota_isverifikasi' => 1,
                'nota_jenis' => 'BELI'
            );
            $id = $this->input->post('id');
            $qty = $this->input->post('qty');
            $hb = $this->input->post('hb');
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
                        'kadaluarsa' => $kadaluarsa[$key],
                        'nobatch' => $nobatch[$key]
                    );
                    $total += $hb[$key] * $qty[$key];
                    array_push($detail, $temp);
                }
            }
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
            $q = $this->Transaksimodel->sv_barangmasuk($data, $detail, $operasional, $this->input->post('rekening'));
            if ($q) {
                $arr['ind'] = 1;
                $arr['msg'] = 'Data telah tersimpan';
            } else {
                $arr['ind'] = 0;
                $arr['msg'] = 'Terjadi kesalahan, Data gagal disimpan';
            }
            echo json_encode($arr);
        }

        function ssplistnota_($bulan = '', $tahun = '')
        {
            $isadmin = ($this->session->userdata('log_level') == 1) ? TRUE : FALSE;
            $aColumns = array('nota_tanggal', 'sup_nama', 'nota_id');
            $sIndexColumn = $aColumns[0];
            $vColumns = array('sup_nama');

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
            $rResult = $this->Transaksimodel->sspntbm($vColumns, $sWhere, $sLimit, $bulan, $tahun);
            $iFilteredTotal = 10;
            $rResultTotal = $this->Transaksimodel->sspntbm_total($sIndexColumn, $sWhere, $bulan, $tahun);
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
                $aksi = '<a id="btn-print" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="left" title="Print Nota" href="' . base_url('barangmasuk/cetak/' . $aRow['nota_id']) . '" target="_blank"><i class="fa fa-print"></i></a> <a id="btn-select" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="left" title="Detail Nota" data-id="' . $aRow['nota_id'] . '"><i class="fa fa-arrow-right"></i></a> '
                    . '<button id="btn-edit" type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#modal-edit" data-id="' . $aRow['nota_id'] . '"><i class="fa fa-pencil"></i></button> ';
                $aksi .= ($isadmin) ? '<a id="btn-delete" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="left" title="Hapus Nota" data-id="' . $aRow['nota_id'] . '"><i class="fa fa-trash"></i></a>' : '';
                $row = array($tanggal, $aRow['sup_nama'], $aRow['detail'], $aksi);
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
        }

        function cetak($id)
        {
            $output['data'] = $this->Transaksimodel->get_ntbm($id);
            $output['info'] = $this->ascfunc->info_();
            $this->load->view('laporan/cetakbm', $output);
        }

        function getnota_($id)
        {
            $data = $this->Transaksimodel->get_ntbm($id);
            $arr['content'] = $this->nota_($data);
            echo json_encode($arr);
        }

        private function nota_($data)
        {
            $this->load->helper('terbilang');
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
                        <td>: <?= $data['tujuan'] ?></td>
                    </tr>
                </tbody>
            </table>

        </div>
        <div class="col-md-5 col-xs-5">
            <h3 style="margin: 0;"><span style="font-style: italic">FAKTUR PEMBELIAN</span> <span class="pull-right" style="font-weight: bold;"><?= $data['nota_id'] ?></span></h3>
            <br>
            <p style="margin-bottom: 0; margin-top: 35px; font-weight: bold">SUPPLIER</p>
            <p style="margin-bottom: 0;"><?= $data['sup_pj'] . ' - ' . $data['sup_nama'] ?></p>
            <p style="margin-bottom: 0"><?= $data['sup_alamat'] ?></p>
            <p><?= $data['sup_telp'] ?></p>
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
                            $total = 0;
                            foreach ($data['detail'] as $val) {
                                $jumlah = ($val['dtn_jumlah'] == '') ? '' : $val['dtn_jumlah'] * $val['dtn_hargabeli'];
                                $total += $jumlah;
                                ?>
                        <tr>
                            <td><?= $val['prd_kode'] ?></td>
                            <td><?= $val['prd_nama'] ?></td>
                            <td class="text-right"><?= $this->ascfunc->nf_($val['dtn_jumlah']) ?></td>
                            <td><?= $val['prd_satuan'] ?></td>
                            <td class="text-right"><?= $this->ascfunc->nf_($val['dtn_hargabeli']) ?></td>
                            <td class="text-right"><?= $this->ascfunc->nf_($jumlah) ?></td>
                        </tr>
                    <?php
                                $ppn = $total * $data['nota_ppn'] / 100;
                                $gtotal = $total + $ppn;
                            }
                            ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-right" colspan="5" style="border-top: 1px solid #000;">TOTAL = Rp.</td>
                        <td class="text-right" style="border-top: 1px solid #000;"><?= $this->ascfunc->nf_($total) ?></td>
                    </tr>
                    <tr>
                        <td class="text-right" colspan="5">PPN(<?= $data['nota_ppn'] . '%' ?>) = Rp.</td>
                        <td class="text-right"><?= $this->ascfunc->nf_($ppn) ?></td>
                    </tr>
                    <tr>
                        <td class="text-right" colspan="5" style="border-top: 1px solid #000;">GRAND TOTAL = Rp.</td>
                        <td class="text-right" style="border-top: 1px solid #000;"><?= $this->ascfunc->nf_($gtotal) ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" style="border-top: 1px solid #000; font-size: 17px;"><?= 'Terbilang : <span style="font-style: italic;">' . ucwords(number_to_words($gtotal)) . ' Rupiah</span>'; ?></td>
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
        </div>
        <div class="col-md-4 col-xs-4 text-center">
            Diketahui Oleh<br><br><br><br>
            (<?= $data['nota_pj'] ?>)
        </div>
    <?php
            $content = ob_get_contents();
            ob_clean();
            return $content;
        }

        function delete_($id)
        {
            $q = $this->Transaksimodel->del_bm($id);
            if ($q) {
                $arr['ind'] = 1;
                $arr['msg'] = 'Data berhasil dihapus';
            } else {
                $arr['ind'] = 0;
                $arr['msg'] = 'Terjadi kesalahan, Data gagal dihapus';
            }
            echo json_encode($arr);
        }

        function modaledit_($id)
        {
            $data = $this->Transaksimodel->edit_ntbm($id);
            $armada = $this->Umodel->li_data('m_armada');
            $tanggal = date('d/m/Y H:i', strtotime($data['nota_tanggal'] . ' ' . $data['nota_jam']));
            ob_start();
            ?>
        <div class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Edit Nota</h4>
                    </div>
                    <div class="modal-body">
                        <form id="form-data">
                            <input type="hidden" class="form-control" name="nid" value="<?= $data['nota_id'] ?>">
                            <input type="hidden" class="form-control" name="supplier" value="<?= $data['nota_asal'] ?>">
                            <input type="hidden" class="form-control" name="gudang" value="<?= $data['nota_tujuan'] ?>">
                            <input type="hidden" class="form-control" name="rekening" value="<?= $data['rekening'] ?>">
                            <input type="hidden" class="form-control" name="tanggal" value="<?= $tanggal ?>">
                            <input type="hidden" class="form-control" name="ppn" value="<?= $data['nota_ppn'] ?>">
                            <input type="hidden" class="form-control" name="kredit" value="<?= $data['nota_iskredit'] ?>">
                            <input type="hidden" class="form-control" name="pj" value="<?= $data['nota_pj'] ?>">
                            <div class="row form-group">
                                <label class="col-md-2 col-xs-4">Supplier</label>
                                <label class="col-md-10 col-xs-8"><?= $data['sup_nama'] ?></label>
                            </div>
                            <div class="row form-group">
                                <label class="col-md-2 col-xs-3">No. Rekening</label>
                                <label class="col-md-10 col-xs-9"><?= $data['rekening'] ?></label>
                            </div>
                            <div class="row form-group" style="margin-bottom: 10px">
                                <label class="col-md-2 col-xs-3">Tanggal</label>
                                <label class="col-md-3 col-xs-4"><?= $tanggal ?></label>
                            </div>
                            <table id="dt-nota" class="table table-striped table-bordered" cellspacing="0" width="100%" style="margin-bottom: 20px">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 16%">No. Batch</th>
                                        <th class="text-center" style="width: 10%">Qty</th>
                                        <th class="text-center" style="width: 5%">Satuan</th>
                                        <th class="text-center" style="width: 23%">Nama Barang</th>
                                        <th class="text-center" style="width: 13%">Harga</th>
                                        <th class="text-center" style="width: 15%">Jumlah</th>
                                        <th class="text-center" style="width: 18%">Expired</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                            $readonly = ($this->session->userdata('log_level') == 1) ? '' : 'readonly=""';
                                            foreach ($data['detail'] as $val) {
                                                ?>
                                        <tr id="<?= $val['prd_id'] ?>">
                                            <td>
                                                <input type="hidden" name="id[]" value="<?= $val['prd_id'] ?>">
                                                <div class="form-group">
                                                    <input type="text" id="nobatch" name="nobatch[]" class="form-control input-sm text-right" autocomplete="off" value="<?= $val['dtn_nobatch'] ?>" maxlenght="15" <?= $readonly ?>>
                                                </div>
                                            </td>
                                            <td>

                                                <div class="form-group">
                                                    <input type="text" id="qty" name="qty[]" class="form-control input-sm text-right" autocomplete="off" value="<?= $val['dtn_jumlah'] ?>" maxlenght="15" <?= $readonly ?>>
                                                </div>
                                            </td>
                                            <td class="text-center"><?= $val['prd_satuan'] ?></td>
                                            <td><?= $val['prd_nama'] ?></td>
                                            <td class="text-right">
                                                <input type="text" id="hb" name="hb[]" class="form-control input-sm text-right" autocomplete="off" value="<?= $val['dtn_hargabeli'] ?>" maxlenght="15">
                                            </td>
                                            <td id="total" class="text-right"><?= number_format($val['dtn_hargabeli'] * $val['dtn_jumlah'], 2, '.', ',') ?></td>

                                            <td class="text-right">
                                                <input type="text" class="form-control kadaluarsa" id="kadaluarsa" name="kadaluarsa[]" value="<?= date('d/m/Y', strtotime($val['dtn_kadaluarsa'])) ?>">
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <div class="row">
                                <label class="col-md-2 col-xs-3">Operasional</label>
                            </div>
                            <hr style="margin-top: 0; margin-bottom: 10px">
                            <table id="dt-op" class="table table-striped table-bordered" cellspacing="0" width="100%" style="margin-bottom: 20px">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 66%">Nama Operasional</th>
                                        <th class="text-center" style="width: 34%">Biaya</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                            foreach ($data['operasional'] as $val) {
                                                if (strpos($val['ope_uraian'], 'Armada') !== false) {
                                                    ?>
                                            <tr>
                                                <td>
                                                    <input type="hidden" class="form-control" name="idarmada" value="<?= $val['ope_id'] ?>">
                                                    <div class="row">
                                                        <div class="col-md-4 col-xs-4"><input type="text" class="form-control" value="Armada" name="armada[]" readonly=""></div>
                                                        <div class="col-md-8 col-xs-8">
                                                            <select class="form-control" name="armada[]" id="armada">
                                                                <option selected="" value="">-Kosong-</option>
                                                                <?php
                                                                                foreach ($armada->result_array() as $varmada) {
                                                                                    $sel = (substr($val['ope_uraian'], 7) == $varmada['arm_jenis'] . ' ' . $varmada['arm_nopol']) ? 'selected=""' : '';
                                                                                    ?>
                                                                    <option <?= $sel ?> value="<?= $varmada['arm_jenis'] . ' ' . $varmada['arm_nopol'] ?>"><?= $varmada['arm_jenis'] . ' [' . $varmada['arm_nopol'] . ']' ?></option>
                                                                <?php
                                                                                }
                                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control text-right" name="biayaarmada" value="<?= $val['ope_biaya'] ?>" autocomplete="off">
                                                </td>
                                            </tr>
                                        <?php
                                                    } elseif (strpos($val['ope_uraian'], 'Sopir') !== false) {
                                                        ?>
                                            <tr>
                                                <td>
                                                    <input type="hidden" class="form-control" name="idsopir" value="<?= $val['ope_id'] ?>">
                                                    <div class="row">
                                                        <div class="col-md-4 col-xs-4"><input type="text" class="form-control" value="Sopir" name="sopir[]" readonly=""></div>
                                                        <div class="col-md-8 col-xs-8"><input type="text" class="form-control" value="<?= substr($val['ope_uraian'], 6) ?>" name="sopir[]" id="sopir"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control text-right" name="biayasopir" value="<?= $val['ope_biaya'] ?>" autocomplete="off">
                                                </td>
                                            </tr>
                                        <?php
                                                    } else {
                                                        ?>
                                            <tr>
                                                <td>
                                                    <input type="hidden" class="form-control" name="idop[]" value="<?= $val['ope_id'] ?>">
                                                    <input type="text" class="form-control" name="op[]" value="<?= $val['ope_uraian'] ?>">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control text-right" name="biaya[]" value="<?= $val['ope_biaya'] ?>">
                                                </td>
                                            </tr>
                                    <?php
                                                }
                                            }
                                            ?>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button id="btn-update" type="button" class="btn btn-success"><i class="fa fa-save"></i> Edit</button>
                    </div>
                </div>
            </div>
        </div>
<?php
        $arr['modal'] = ob_get_contents();
        ob_clean();
        echo json_encode($arr);
    }
}
