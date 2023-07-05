<?php

namespace app;
require_once('../vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\IOFactory;
class Excel
{
    private string $path;
    public function __construct($path)
    {
        $path = glob($path);
        if(empty($path)) {
            die('File not exists or invalid file format');
        } else {
            $this->path = $path[0];
        }
    }
    public function getItemsColumn($column, $numberRow = 0): array
    {
        $itemsColumn = [];
        $spreadsheet = IOFactory::load($this->path);
        $sheet = $spreadsheet->getActiveSheet();
        for ($numberRow; ($sheet->getCell($column . $numberRow)->getValue()); $numberRow++) {
            $itemsColumn[] = $sheet->getCell($column .$numberRow)->getValue();
        }
        return $itemsColumn;
    }
    public function writeColumn($column, $value, $numberRow = 1): void
    {
        $spreadsheet = IOFactory::load($this->path);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue($column . $numberRow, $value);
        try {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($this->path);
            $spreadsheet->disconnectWorksheets();
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
            die("Error write: " . $e);
        }
    }

}