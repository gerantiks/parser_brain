<?php
//Обробка файлу xlsx для внесення його в БД

require_once('../vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = glob('../uploads/' . "*");
$spreadsheet = IOFactory::load($filePath[0]);
$sheet = $spreadsheet->getActiveSheet();

$arrayCode = [];
$arrayPrice = [];
for ($i=2; ($sheet->getCell("A$i")->getValue()); $i++) {
    $arrayCode[] = $sheet->getCell("A$i")->getValue();
    $arrayPrice[] = $sheet->getCell("B$i")->getValue();
}
