<?php
$start = microtime(true);
require_once('../private/information_api.php');
require_once('../vendor/autoload.php');
require_once('./parsing.php');

use PhpOffice\PhpSpreadsheet\IOFactory;

$authentication= new \app\CurlRequest('http://api.brain.com.ua/auth');
$params = [
    'login' => LOGIN,
    'password' => PASSWORD
];

$answer = $authentication->post($params, 1);
$sid = $answer['result'];

$file = new \app\Files('../import-file/example-table.xlsx');
$file->Copy('../import-file/' .'result.xlsx');
$path = \app\Files::Find('../import-file/', 'result.xlsx');

$i = 0;
$numberRow = 2;
while ($arrayCodes[$i++]) {  // перевірка наявності
    $request = new \app\CurlRequest("http://api.brain.com.ua/product/product_code/$arrayCodes[$i]/$sid?lang=ua");
    $product = $request->get('', 1);
    empty($product['result']['available'])? $avaliable = '-' : $avaliable = "+";
    $write = new \app\Excel($path);
    $write->writeColumn('L', $avaliable, $numberRow++);
}

$logout = new \app\CurlRequest("http://api.brain.com.ua/logout/$sid");//ліквідація сесії SID
$logout->post();

$end = microtime(true);
$executionTime = $end - $start;
echo "Success write";
echo "\nTime: " . round($executionTime) . "s.";