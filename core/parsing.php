<?php
//Обробка файлу xlsx для внесення його в БД

require_once('../vendor/autoload.php');

$path = \app\Files::Find('../uploads/', '*.xlsx');
$excel = new \app\Excel($path);
$arrayCodes = $excel->getItemsColumn('A', 2);
$excel->getItemsColumn('B', 2);





