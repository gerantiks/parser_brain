<?php
date_default_timezone_set('Europe/Kyiv');
$start = microtime(true);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/secret_info.php';
require_once AUTOLOAD;
require_once APP . '/functions.php';

use classes\Excel;
use classes\File;

$path = File::find(ROOT . '/update_file/', '*');
if(File::getFormatFile($path)) {  //якщо вірний формат парсимо
    $excel = new excel($path);
    $arrayCodes = $excel->getItemsColumn('Q', 2);
} else {
    die('Файл не вірного формату!');
}

$client = new GuzzleHttp\Client();
$sid = authorization($client);

$urls = [];
$index = 0;
$i = 1;
$lastRow = 2;

foreach ($arrayCodes as $code) {
    $urls[] = URL_BRAIN . "/product/product_code/$code/$sid?lang=ua";
}

foreach ($urls as $url) {
    $data = getData($client, $url);

    if($data['status'] === 0) { // поверне массив з помилкою в коді товару;
        echo "Помилка в позиції: $arrayCodes[$index] - перевірте код товару!\n";
        $index++;
        continue;
    }

    $position = [
        'avaliable' => empty($data['result']['available']) ? $avaliable = "-" : $avaliable = "+",
        'price'     => getAveragePrice($data['result']['price_uah'], $data['result']['retail_price_uah'], $arrayCoefficient[$index] = 0.5)
    ];
    $data = [
        'H' => $position['price'],
        'L' => $position['avaliable'],

    ];

    $write = new Excel($path);
    $write->writeColumn($position['price'], 'H', $lastRow);
    $write->writeColumn($position['avaliable'], 'L', $lastRow);
    print_r("Оновлено позицій: ". $i++ . PHP_EOL);

    $index++;
    $lastRow++;
}

logout($client, $sid);
$executionTime = microtime(true) - $start;
echo "Файл успішно оновлено ! Час оновлення склав: " . round($executionTime) . " секунди.";
