<?php

include_once 'sfBaseBoleto.class.php';
include_once 'sfBoleto.class.php';
include_once 'sfBlocoBoleto.class.php';
/*
$carne = new sfBlocoBoleto('HSBC');
$carne->setImagePath('/sfBoletoPlugin/images/');
echo $carne->renderPaginaExemplo(5);
*/

$boleto = sfBoleto::create('HSBC');
$boleto->setImagePath('/sfBoletoPlugin/images/');
//echo $boleto->setCarteira('PUTZ');
//echo $boleto->renderPaginaExemplo(5);
echo $boleto->renderCarneExemplo(5);

/*

$boleto_html =  $boleto->renderCarneExemplo(5);

$pdf = new TCPDF();
//$pdf->setPageOrientation('L');
//$pdf->SetFont("FreeSerif", "", 12);
$pdf->SetMargins(0, 0, 0);
//$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(false);
$pdf->SetTitle('MKWeb Contrato');

//    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

//$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
//$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// init pdf doc
$pdf->AliasNbPages();
$pdf->AddPage();

// output some HTML code
$pdf->writeHTML($boleto_html, true, 0);
$pdf->wri

// output
//$pdf->Output();

include_once '../lib/vendor/dompdf/dompdf_config.inc.php';

$dompdf = new DOMPDF();
$dompdf->set_paper('A4', 'landscape');
$dompdf->load_html($boleto_html);
$dompdf->render();
$dompdf->stream("sample.pdf");
*/
