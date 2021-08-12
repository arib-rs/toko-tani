<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Kasmodel');
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
        $header = $this->ascfunc->header_('kas-menu');
        $header['css'] = array('assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $body = array(
            'cbrek' => $this->cbrek_()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('kas/kas', $body);
        $this->load->view('templates/footer');
    }

    function cbrek_() {
        if ($this->session->userdata('log_tipe') != '') {
            $data = $this->Umodel->get_data('m_rekening', array('rek_kategori' => $this->session->userdata('log_tipe')));
        } else {
            $data = $this->Umodel->li_data('m_rekening');
        }
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

    function filterdata_() {
        $arr = $this->content_($this->input->post('tanggal'), $this->input->post('rekening'));
        echo json_encode($arr);
    }

    private function content_($tanggal, $rekening) {
        $tgl = explode('/', $tanggal);
        ob_start();
        $tm = date('Y-m-d', strtotime($tgl[1] . '-' . $tgl[0] . '-01'));
        $ta = date('Y-m-t', strtotime($tm));
        $saldo = $this->Kasmodel->saldoawal($tm, $rekening);
        $data = $this->Kasmodel->rekapkas($tm, $ta, $rekening);
        $no = 1;
        $total = array(
            'debet' => 0,
            'kredit' => 0
        );
        $iskredit = (is_null($saldo['kredit'])) ? '5px' : '30px';
        ?>
        <tr>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-bold" style="padding-left: <?= $iskredit ?>">Saldo Awal</td>
            <td class="text-right"><?= $this->ascfunc->nf_($saldo['debet']) ?></td>
            <td class="text-right"><?= $this->ascfunc->nf_($saldo['kredit']) ?></td>
        </tr>
        <?php
        $total['debet'] += $saldo['debet'];
        $total['kredit'] += $saldo['kredit'];
        foreach ($data as $val) {
            $keterangan = ($val['kas_keterangan'] == '') ? '' : '<br>' . $val['kas_keterangan'];
            $iskredit = (is_null($val['kas_kredit'])) ? '5px' : '30px';
            ?>
            <tr>
                <td class="text-center"><?= $no ?></td>
                <td class="text-center"><?= $val['tgl'] ?></td>
                <td style="padding-left: <?= $iskredit ?>"><?= $val['kas_uraian'] . $keterangan ?></td>
                <td class="text-right"><?= $this->ascfunc->nf_($val['kas_debet']) ?></td>
                <td class="text-right"><?= $this->ascfunc->nf_($val['kas_kredit']) ?></td>
            </tr>
            <?php
            $total['debet'] += $val['kas_debet'];
            $total['kredit'] += $val['kas_kredit'];
            $no++;
        }
        $result['tbody'] = ob_get_contents();
        ob_clean();
        ?>
        <tr class="bg-gray-active text-bold">
            <td class="text-right" colspan="3">Jumlah</td>
            <td class="text-right"><?= $this->ascfunc->nf_($total['debet']) ?></td>
            <td class="text-right"><?= $this->ascfunc->nf_($total['kredit']) ?></td>
        </tr>
        <tr class="bg-gray-active text-bold">
            <td class="text-right" colspan="3">Saldo</td>
            <td class="text-center" colspan="2"><?= $this->ascfunc->nf_(abs($total['debet'] - $total['kredit'])) ?></td>
        </tr>
        <?php
        $result['tfoot'] = ob_get_contents();
        ob_clean();
        return $result;
    }

    function ssplist_($jenis, $bulan = '', $tahun = '') {
        $aColumns = array('kas_tanggal', 'kas_uraian', 'kas_debet', 'kas_kredit', 'kas_id');
        $sIndexColumn = $aColumns[4];
        $vColumns = array('kas_uraian');

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
        $rResult = $this->Kasmodel->ssplist_($vColumns, $sWhere, $sLimit, $jenis, $bulan, $tahun);
        $iFilteredTotal = 10;
        $rResultTotal = $this->Kasmodel->ssplist_total($sIndexColumn, $sWhere, $jenis, $bulan, $tahun);
        $iTotal = $rResultTotal;

        $iFilteredTotal = $iTotal;
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

        foreach ($rResult as $aRow) {
            $tanggal = date('d/m/Y', strtotime($aRow['kas_tanggal'])) . ' ' . date('H:i', strtotime($aRow['kas_jam']));
            $keterangan = ($aRow['kas_keterangan'] == '') ? '' : '<br>' . $aRow['kas_keterangan'];
            $aksi = '<a id="btn-edit" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="left" title="Edit Kas" data-id="' . $aRow['kas_id'] . '"><i class="fa fa-pencil"></i></a> <a id="btn-delete" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="left" title="Hapus Kas" data-id="' . $aRow['kas_id'] . '"><i class="fa fa-trash"></i></a>';
            if ($jenis == 'pengeluaran') {
                $row = array($tanggal, $aRow['kas_uraian'] . $keterangan, $this->ascfunc->nf_($aRow['kas_kredit']), $aksi);
            } else {
                $row = array($tanggal, $aRow['kas_uraian'] . $keterangan, $this->ascfunc->nf_($aRow['kas_debet']), $this->ascfunc->nf_($aRow['kas_kredit']), $aksi);
            }
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    function edit_($id) {
        $data = $this->Umodel->get_data('d_kas', array('kas_id' => $id))->row_array();
        $arr = $data;
        $arr['tanggal'] = date('d/m/Y H:i', strtotime($data['kas_tanggal'] . ' ' . $data['kas_jam']));
        echo json_encode($arr);
    }

    function save_($jenis) {
        $tgl = strtotime(str_replace('/', '-', $this->input->post('tanggal')));
        $data = array(
            'kas_id' => $this->input->post('id'),
            'kas_rekening' => $this->input->post('rekening'),
            'kas_uraian' => $this->input->post('uraian'),
            'kas_keterangan' => ($this->input->post('keterangan') == '') ? NULL : $this->input->post('keterangan'),
            'kas_tanggal' => date('Y-m-d', $tgl),
            'kas_jam' => date('H:i:s', $tgl),
            'kas_debet' => ($this->input->post('debet') == '') ? NULL : $this->input->post('debet'),
            'kas_kredit' => ($this->input->post('kredit') == '') ? NULL : $this->input->post('kredit'),
            'kas_jenis' => $jenis
        );
        $q = $this->Kasmodel->sv_kas($data);
        if ($q) {
            $arr['ind'] = 1;
            $arr['msg'] = 'Data telah tersimpan';
        } else {
            $arr['ind'] = 0;
            $arr['msg'] = 'Terjadi kesalahan, Data gagal disimpan';
        }
        echo json_encode($arr);
    }

    function delete_($id) {
        $q = $this->Umodel->del_data('d_kas', array('kas_id' => $id));
        if ($q) {
            $arr['ind'] = 1;
            $arr['msg'] = 'Data telah dihapus';
        } else {
            $arr['ind'] = 0;
            $arr['msg'] = 'Terjadi kesalahan, Data gagal dihapus';
        }
        echo json_encode($arr);
    }

    public function obm() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(3);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('kasobm-menu');
        $header['css'] = array('assets/plugin/datetimepicker/css/bootstrap-datetimepicker.min.css', 'assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/js/bootstrap-typeahead.js', 'assets/plugin/moment/moment.min.js', 'assets/plugin/datetimepicker/js/bootstrap-datetimepicker.min.js', 'assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $body = array(
            'cbrek' => $this->cbrek_()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('kas/obm', $body);
        $this->load->view('templates/footer');
    }

    public function prive() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(3);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('kasprive-menu');
        $header['css'] = array('assets/plugin/datetimepicker/css/bootstrap-datetimepicker.min.css', 'assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/js/bootstrap-typeahead.js', 'assets/plugin/moment/moment.min.js', 'assets/plugin/datetimepicker/js/bootstrap-datetimepicker.min.js', 'assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $body = array(
            'cbrek' => $this->cbrek_()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('kas/prive', $body);
        $this->load->view('templates/footer');
    }

    public function kaskecil() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(3);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('kaskaskecil-menu');
        $header['css'] = array('assets/plugin/datetimepicker/css/bootstrap-datetimepicker.min.css', 'assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/js/bootstrap-typeahead.js', 'assets/plugin/moment/moment.min.js', 'assets/plugin/datetimepicker/js/bootstrap-datetimepicker.min.js', 'assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $body = array(
            'cbrek' => $this->cbrek_()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('kas/kaskecil', $body);
        $this->load->view('templates/footer');
    }

    public function notifikasi() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(3);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('kasnotifikasi-menu');
        $header['css'] = array('assets/plugin/datetimepicker/css/bootstrap-datetimepicker.min.css', 'assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/js/bootstrap-typeahead.js', 'assets/plugin/moment/moment.min.js', 'assets/plugin/datetimepicker/js/bootstrap-datetimepicker.min.js', 'assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $body = array(
            'cbrek' => $this->cbrek_()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('kas/notifikasi', $body);
        $this->load->view('templates/footer');
    }

    public function lainnya() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(3);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('kaslainnya-menu');
        $header['css'] = array('assets/plugin/datetimepicker/css/bootstrap-datetimepicker.min.css', 'assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/js/bootstrap-typeahead.js', 'assets/plugin/moment/moment.min.js', 'assets/plugin/datetimepicker/js/bootstrap-datetimepicker.min.js', 'assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $body = array(
            'cbrek' => $this->cbrek_()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('kas/lainnya', $body);
        $this->load->view('templates/footer');
    }

    public function pajak() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(3);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('kaspajak-menu');
        $header['css'] = array('assets/plugin/datetimepicker/css/bootstrap-datetimepicker.min.css', 'assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/js/bootstrap-typeahead.js', 'assets/plugin/moment/moment.min.js', 'assets/plugin/datetimepicker/js/bootstrap-datetimepicker.min.js', 'assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $body = array(
            'cbrek' => $this->cbrek_()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('kas/pajak', $body);
        $this->load->view('templates/footer');
    }

    public function operasional() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $allowedlevel = array(3);
        if (!in_array($this->session->userdata('log_level'), $allowedlevel)) {
            show_error("Anda tidak diperbolehkan mengakses halaman ini. <br><a href='" . base_url() . "'>kembali</a>", 403, "Forbidden Page Access");
            exit();
        }
        $header = $this->ascfunc->header_('kasoperasional-menu');
        $header['css'] = array('assets/plugin/datetimepicker/css/bootstrap-datetimepicker.min.css', 'assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/js/bootstrap-typeahead.js', 'assets/plugin/moment/moment.min.js', 'assets/plugin/datetimepicker/js/bootstrap-datetimepicker.min.js', 'assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $body = array(
            'cbrek' => $this->cbrek_()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('kas/operasional', $body);
        $this->load->view('templates/footer');
    }

    function excel_() {
        $rekening = $this->input->get('rekening');
        $tanggal = urldecode($this->input->get('tanggal'));
        $tgl = explode('/', $tanggal);
        $tm = date('Y-m-d', strtotime(str_replace('/', '-', '01/' . $tanggal)));
        $ta = date('Y-m-t', strtotime($tm));
        $saldo = $this->Kasmodel->saldoawal($tm, $rekening);
        $data = $this->Kasmodel->rekapkas($tm, $ta, $rekening);
        $total = array(
            'debet' => 0,
            'kredit' => 0
        );
        $iskredit = (is_null($saldo['kredit'])) ? '' : '          ';

        $this->load->library('PHPExcel');
        $excel = new PHPExcel();
        $sh1 = $excel->setActiveSheetIndex(0);
        $sh1->setCellValue('A1', 'KEUANGAN KAS HARIAN');
        $excel->getActiveSheet()->mergeCells('A1:E1');
        $sh1->setCellValue('A2', 'Bulan ' . $this->ascfunc->bulan_[$tgl[0]] . ' ' . $tgl[1]);
        $excel->getActiveSheet()->mergeCells('A2:E2');
        $sh1->setCellValue('A3', 'Rek : ' . $rekening);
        $excel->getActiveSheet()->mergeCells('A3:E3');

        //header
        $sh1->setCellValue('A4', 'No');
        $sh1->setCellValue('B4', 'Tanggal');
        $sh1->setCellValue('C4', 'Uraian');
        $sh1->setCellValue('D4', 'Debet');
        $sh1->setCellValue('E4', 'Kredit');

        //content
        $no = 1;
        $sh1->setCellValue('A5', '');
        $sh1->setCellValue('B5', '');
        $sh1->setCellValue('C5', $iskredit . 'Saldo Awal');
        $sh1->setCellValue('D5', $this->ascfunc->nf_($saldo['debet']));
        $sh1->setCellValue('E5', $this->ascfunc->nf_($saldo['kredit']));
        $row = 6;
        $total['debet'] += $saldo['debet'];
        $total['kredit'] += $saldo['kredit'];
        foreach ($data as $val) {
            $iskredit = (is_null($val['kas_kredit'])) ? '' : '          ';
            $keterangan = ($val['kas_keterangan'] == '') ? '' : "\n" . $val['kas_keterangan'];
            $sh1->setCellValue('A' . $row, $no);
            $sh1->setCellValue('B' . $row, $val['tgl']);
            $sh1->setCellValue('C' . $row, $iskredit . $val['kas_uraian'] . $keterangan);
            $sh1->setCellValue('D' . $row, $this->ascfunc->nf_($val['kas_debet']));
            $sh1->setCellValue('E' . $row, $this->ascfunc->nf_($val['kas_kredit']));
            $total['debet'] += $val['kas_debet'];
            $total['kredit'] += $val['kas_kredit'];
            $no++;
            $row++;
        }
        //total jumlah
        $sh1->setCellValue('A' . $row, 'Jumlah');
        $excel->getActiveSheet()->mergeCells("A$row:C$row");
        $sh1->setCellValue('D' . $row, $this->ascfunc->nf_($total['debet']));
        $sh1->setCellValue('E' . $row, $this->ascfunc->nf_($total['kredit']));
        $row++;
        $sh1->setCellValue('A' . $row, 'Saldo');
        $excel->getActiveSheet()->mergeCells("A$row:C$row");
        $sh1->setCellValue('D' . $row, $this->ascfunc->nf_($total['debet'] - $total['kredit']));
        $excel->getActiveSheet()->mergeCells("D$row:E$row");

        //style
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
                        'size' => 11,
                        'name' => 'Calibri'),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0)
        ));
        $titlesubStyle->applyFromArray(
                array(
                    'font' => array(
                        'bold' => false,
                        'size' => 11,
                        'name' => 'Calibri'),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0)
        ));
        $detailStyle->applyFromArray(
                array(
                    'font' => array(
                        'bold' => false,
                        'size' => 16,
                        'name' => 'Calibri'),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0)
        ));
        $headerStyle->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'size' => 11,
                        'name' => 'Calibri'),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'rotation' => 0),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('argb' => 'FFEEEEEE')),
                    'borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                    )
        ));
        $bodyStyle->applyFromArray(
                array('font' => array(
                        'size' => 11,
                        'name' => 'Calibri'),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                        'rotation' => 0,
                        'wrap' => TRUE),
                    'borders' => array(
                        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                    )
        ));
        $centerStyle->applyFromArray(
                array('font' => array(
                        'size' => 11,
                        'name' => 'Calibri'),
                    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'borders' => array(
                        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                    )
        ));
        $centerBoldStyle->applyFromArray(
                array('font' => array(
                        'bold' => true,
                        'size' => 11,
                        'name' => 'Calibri'),
                    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('argb' => 'FFEEEEEE')),
                    'borders' => array(
                        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                    )
        ));
        $rightStyle->applyFromArray(
                array('font' => array(
                        'size' => 11,
                        'name' => 'Calibri'),
                    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT),
                    'borders' => array(
                        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                    )
        ));
        $rightBoldStyle->applyFromArray(
                array('font' => array(
                        'bold' => true,
                        'size' => 11,
                        'name' => 'Calibri'),
                    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('argb' => 'FFEEEEEE')),
                    'borders' => array(
                        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                        'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                    )
        ));
        $baris = $row - 2;
        $excel->getActiveSheet()->setSharedStyle($titleStyle, "A1:E1");
        $excel->getActiveSheet()->setSharedStyle($titlesubStyle, "A2:E2");
        $excel->getActiveSheet()->setSharedStyle($detailStyle, "A3:E3");
        $excel->getActiveSheet()->setSharedStyle($headerStyle, "A4:E4");
        $excel->getActiveSheet()->setSharedStyle($bodyStyle, "A5:E$baris");
        $excel->getActiveSheet()->setSharedStyle($centerStyle, "A5:B$baris");
        $excel->getActiveSheet()->setSharedStyle($rightStyle, "D5:E$baris");

        $barismin = $row - 1;
        $excel->getActiveSheet()->setSharedStyle($headerStyle, "A$barismin:E$row");
        $excel->getActiveSheet()->setSharedStyle($centerBoldStyle, "A$barismin:A$row");
        $excel->getActiveSheet()->setSharedStyle($rightBoldStyle, "D$barismin:E$row");

        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(4.57);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(58.43);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(17);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(17);

        $excel->getActiveSheet()->setTitle('RINCIAN KEU');
        $excel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=KAS_' . $rekening . '_' . $this->ascfunc->bulan_[$tgl[0]] . '-' . $tgl[1] . '.xlsx');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $objWriter->save('php://output');
    }

    function pdf_() {
        if (!$this->session->userdata('log_user')) {
            redirect(base_url('login'));
        }
        $this->load->model('Laporanmodel');
        $rekening = $this->input->get('rekening');
        $tanggal = urldecode($this->input->get('tanggal'));
        $tgl = explode('/', $tanggal);
        $data = $this->content_($tanggal, $rekening);
        $data['info'] = $this->ascfunc->info_();
        $data['tanggal'] = $this->ascfunc->bulan_[$tgl[0]] . ' ' . $tgl[1];
        $this->load->helper(array('dompdf', 'file'));
        $html = $this->load->view('pdf/kas', $data, true);
        pdf_create($html, 'KAS_' . $rekening . '_' . $this->ascfunc->bulan_[$tgl[0]] . '-' . $tgl[1], "landscape");
    }

}
