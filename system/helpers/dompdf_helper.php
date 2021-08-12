<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function pdf_create($html, $filename, $orientation = "portrait", $paper_size = "a4", $stream = TRUE) {
    require_once("dompdf/dompdf_config.inc.php");

    ini_set("memory_limit", "-1");
    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    $dompdf->set_paper($paper_size, $orientation);
    $dompdf->render();
    $dompdf->stream($filename . ".pdf", array("Attachment" => 0));
}
