<?php
date_default_timezone_set('Europe/Kyiv');
$start = microtime(true);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/secret_info.php';
require_once AUTOLOAD;
require_once APP . '/functions.php';

use classes\Excel;
use classes\File;

$path = File::find(__DIR__ . "/create_new_file/file_download/", '*');


if(File::getFormatFile($path)) {  //якщо вірний формат парсимо
    $excel = new excel($path);
    $arrayCodes = $excel->getItemsColumn('A', 2);
    $arrayCoefficient = $excel->getItemsColumn('B', 2);
} else {
    die('Файл не вірного формату!');
}

$client = new GuzzleHttp\Client();
$sid = authorization($client);

$index = 0;
$lastRow = 1;
$i = 1;
$urls = [];

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

    $urlLink = URL_BRAIN . "/product_pictures/{$data['result']['productID']}/$sid";
    $linkPhoto = getData($client, $urlLink);

    $position = [
        'name'             => $data['result']['name'],
        'productId'        => $data['result']['productID'],
        'avaliable'        => empty($data['result']['available']) ? $avaliable = "-" : $avaliable = "+",
        'article'          => strlen($data['result']['articul']) > 25 ? $article = $data['result']['EAN']: $data['result']['articul'],
        'briefDescription' => $data['result']['brief_description'],
        'description'      => stripTagsText(empty($product['result']['description']) ? $data['result']['brief_description'] :
                              $data['result']['description']),
        'id'               => $data['result']['productID'],
        'link'             => getlistLinks($linkPhoto['result'], 7),
        'price'            => getAveragePrice($data['result']['price_uah'], $data['result']['retail_price_uah'], $arrayCoefficient[$index])
    ];
    $data = [
        'A' => $position['article'],
        'B' => $position['name'],
        'C' => $position['name'],
        'D' => "{$position['article']}, $arrayCodes[$index]",
        'E' => "{$position['article']}, $arrayCodes[$index]",
        'F' => $position['description'],
        'G' => $position['description'],
        'H' => $position['price'],
        'I' => "UAH",
        'J' => "шт.",
        'K' => $position['link'],
        'L' => $position['avaliable'],
        'M' => $position['article'],
        'N' => "Загальні характеристики",
        'O' => '',
        'P' => $position['briefDescription'],
        'Q' => $arrayCodes[$index]
    ];

    if($index === 0) {
        $file = new File(__DIR__ . '/config/example-table.xlsx');
        $fileName = date('H-i__d-m-Y');
        $file->Copy(__DIR__ . "/create_new_file/$fileName-import.xlsx");
    }
    $write = new Excel(__DIR__ . "/create_new_file/$fileName-import.xlsx");
    $write->writeArray($data, ++$lastRow);
    print_r("Оброблені позиції: ". $i++ . PHP_EOL);

    $index++;
}

logout($client, $sid);
$executionTime = microtime(true) - $start;
echo "Файл успішно створено ! Час обробки склав: " . round($executionTime) . " секунди.";