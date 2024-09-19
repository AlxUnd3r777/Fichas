<?php
// exportar_excel.php - Archivo para exportar fichas a Excel

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fichas'])) {
    $fichas = json_decode($_POST['fichas'], true);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Agregar encabezados
    $sheet->setCellValue('A1', 'Número de Ficha');
    $sheet->setCellValue('B1', 'Número de Archivo');
    $sheet->setCellValue('C1', 'Estantería');
    $sheet->setCellValue('D1', 'Fecha de Creación');
    $sheet->setCellValue('E1', 'Usuario');

    // Agregar datos de fichas
    $row = 2;
    foreach ($fichas as $ficha) {
        $sheet->setCellValue('A' . $row, $ficha['numero']);
        $sheet->setCellValue('B' . $row, $ficha['numero_archivo']);
        $sheet->setCellValue('C' . $row, $ficha['estanteria']);
        $sheet->setCellValue('D' . $row, $ficha['fecha_creacion']);
        $sheet->setCellValue('E' . $row, $ficha['username']);
        $row++;
    }

    // Descargar el archivo Excel
    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="fichas.xlsx"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
}
?>
