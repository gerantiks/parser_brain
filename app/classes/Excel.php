<?php
namespace classes;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

class Excel
{
    private string $path;

    public function __construct(string $path)
    {
        if(!file_exists($path)) {
            echo 'File not exists or invalid file format';
            exit;
        } else {
            $this->path = $path;
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
    public function writeColumn($value, $column, $numberRow = 1): void
    {
        try {
            $spreadsheet = IOFactory::load($this->path);
            $spreadsheet->getActiveSheet()->setCellValue($column . $numberRow, $value);
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($this->path);
            $spreadsheet->disconnectWorksheets();
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
            die("Error write: " . $e);
        }
    }
    public function writeArray($data, $row, $startCell = 'A')
    {
        try {
            $spreadsheet = IOFactory::load($this->path);
            $spreadsheet->getActiveSheet()->fromArray($data, null, $startCell . $row);
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($this->path);
            $spreadsheet->disconnectWorksheets();
        } catch (Exception $e) {
            die("Error write: " . $e);
        }
    }

    public function __destruct()
    {

    }


}