<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tutupbuku extends CI_Controller {

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
        $header = $this->ascfunc->header_('tutupbuku-menu');
        $header['css'] = array('assets/plugin/datepicker/bootstrap-datepicker.css');
        $header['js'] = array('assets/plugin/datepicker/bootstrap-datepicker.js', 'assets/plugin/datepicker/locales/bootstrap-datepicker.id.js');
        $body = array(
            'cbrek' => $this->cbrek_()
        );

        $this->load->view('templates/header', $header);
        $this->load->view('kas/tutupbuku', $body);
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

    private function content_($tahun, $rekening) {
        ob_start();
        $tm = date('Y-m-d', strtotime($tahun . '-01-01'));
        $saldo = $this->Kasmodel->saldoawal($tm, $rekening);
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
        $no = 1;
        for ($i = 1; $i <= 12; $i++) {
            $bln = ($i < 10) ? '0' . $i : $i;
            $tm = date('Y-m-d', strtotime($tahun . '-' . $bln . '-01'));
            $ta = date('Y-m-t', strtotime($tm));
            $data = $this->Kasmodel->rekapkas($tm, $ta, $rekening);
            $tdebet = $tkredit = 0;
            foreach ($data as $val) {
                $tdebet += $val['kas_debet'];
                $tkredit += $val['kas_kredit'];
            }
            ?>
            <tr>
                <td class="text-center"><?= $no ?></td>
                <td class="text-center"><?= date('d/m/Y', strtotime($ta)) ?></td>
                <td>Bulan <?= $this->ascfunc->bulan_[$bln] ?></td>
                <td class="text-right"><?= $this->ascfunc->nf_($tdebet) ?></td>
                <td class="text-right"><?= $this->ascfunc->nf_($tkredit) ?></td>
            </tr>
            <?php
            $total['debet'] += $tdebet;
            $total['kredit'] += $tkredit;
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

    function excel_() {
        $rekening = $this->input->get('rekening');
        $tahun = urldecode($this->input->get('tanggal'));
        $tm = date('Y-m-d', strtotime($tahun . '-01-01'));
        $saldo = $this->Kasmodel->saldoawal($tm, $rekening);
        $total = array(
            'debet' => 0,
            'kredit' => 0
        );
        $iskredit = (is_null($saldo['kredit'])) ? '' : '          ';

        $this->load->library('PHPExcel');
        $excel = new PHPExcel();
        $sh1 = $excel->setActiveSheetIndex(0);
        $sh1->setCellValue('A1', 'KEUANGAN KAS TUTUP BUKU');
        $excel->getActiveSheet()->mergeCells('A1:E1');
        $sh1->setCellValue('A2', 'Tahun ' . $tahun);
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
        for ($i = 1; $i <= 12; $i++) {
            $bln = ($i < 10) ? '0' . $i : $i;
            $tm = date('Y-m-d', strtotime($tahun . '-' . $bln . '-01'));
            $ta = date('Y-m-t', strtotime($tm));
            $data = $this->Kasmodel->rekapkas($tm, $ta, $rekening);
            $tdebet = $tkredit = 0;
            foreach ($data as $val) {
                $tdebet += $val['kas_debet'];
                $tkredit += $val['kas_kredit'];
            }
            $sh1->setCellValue('A' . $row, $no);
            $sh1->setCellValue('B' . $row, date('d/m/Y', strtotime($ta)));
            $sh1->setCellValue('C' . $row, 'Bulan ' . $this->ascfunc->bulan_[$bln]);
            $sh1->setCellValue('D' . $row, $this->ascfunc->nf_($tdebet));
            $sh1->setCellValue('E' . $row, $this->ascfunc->nf_($tkredit));
            $total['debet'] += $tdebet;
            $total['kredit'] += $tkredit;
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
        $sh1->setCellValue('D' . $row, $this->ascfunc->nf_(abs($total['debet'] - $total['kredit'])));
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
        header('Content-Disposition: attachment;filename=KAS_' . $rekening . '_' . $tahun . '.xlsx');
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
        $tahun = urldecode($this->input->get('tanggal'));
        $data = $this->content_($tahun, $rekening);
        $data['info'] = $this->ascfunc->info_();
        $data['tahun'] = $tahun;
        $this->load->helper(array('dompdf', 'file'));
        $html = $this->load->view('pdf/tutupbuku', $data, true);
        pdf_create($html, 'KAS_' . $rekening . '_' . $tahun, "potrait");
    }

}
