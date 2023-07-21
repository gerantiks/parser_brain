<?php
$start = microtime(true);
require_once('../config/config.php');
require_once(PRIVAT . '/information_api.php');
require_once(CORE . '/functions.php');
require_once(AUTOLOAD);
require_once('parsing.php');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$authentication= new \app\CurlRequest(URL.'/auth');
$params = [
    'login' => LOGIN,
    'password' => PASSWORD
];

$answer = $authentication->post($params, 1);
$sid = $answer['result'];


if($check === 'new') {
    $file = new \app\Files(FILE . '/return/example-table.xlsx');
    $file->Copy(FILE .'/return/result.xlsx');
    $path = \app\Files::Find(FILE . '/return/', 'result.xlsx');
} else {
    $path = \app\Files::Find(FILE . '/download/secondary/', '*.xlsx');
}


$i = 0;
$lastRow = 1;

while (is_array($arrayCodes)) {  // перевірка наявності
    $url = URL . '/product/product_code/'. $arrayCodes[$i] . '/'. $sid .'?lang=ua';
    $product = send($url);
    echo "+";
    if($check === 'new') {
        $urlLink = URL . "/product_pictures/{$product['result']['productID']}/$sid";
        $link = send($urlLink);
    }
    require (CONFIG . '/variable.php');

    if($check === 'new') {
        $write = new \app\Excel($path);
        $write->writeArray($data, ++$lastRow);
    } else {
        $write = new \app\Excel($path);
        $write->writeColumn($pos['avaliable'], 'L', ++$lastRow);
    }

    $i < (count($arrayCodes) - 1) ? $i++ : $arrayCodes = false;
}

logout($sid); //ліквідація сесії SID

$end = microtime(true);
$executionTime = $end - $start;
echo "Success write";
echo "\nTime: " . round($executionTime) . "s.";