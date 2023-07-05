<?php
$start = microtime(true);
require_once('../private/information_api.php');
require_once('../vendor/autoload.php');
require_once('parsing.php');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

global $login, $password, $dbname, $host, $userDb, $passwordDb, $database, $arrayCode, $arrayCoefficient;

//Отримуємо SID api Brain

$authentication= new \app\CurlRequest('http://api.brain.com.ua/auth');
$params = [
    'login' => $login,
    'password' => $password
];

try {
    $authentication = $authentication->post($params);
    $responseDecode = json_decode($authentication, true);
    $sid = $responseDecode['result'];
    print_r($sid);
} catch (Exception $e) {
    file_put_contents('../private/logs.txt', date("H:i:s - Y-m-d ").$e->getMessage() . "\n" , FILE_APPEND);
    die;
}
die;

//Ядро обробки
$error = '';
$i = 0;
$lastRow = 2;

while($arrayCode[$i]) {
    $product = getProduct($arrayCode[$i], $sid);
    $name = $product['result']['name'];
    $avaliable = $product['result']['available'];
    empty($avaliable) ? $avaliable = "-" : $avaliable = "+";
    $article = $product['result']['articul'];
    strlen($article) > 25 ? $article = $product['result']['EAN']: $article; //Якщо артикул більше 25 символів то підставляємо EAN код
    $briefDescription = $product['result']['brief_description'];
    $id = $product['result']['productID'];
    $optPrice = $product['result']['price_uah'];
    $retailPrice = $product['result']['retail_price_uah'];
    empty($arrayCoefficient[$i]) ? $coefficient = 0.5 : $coefficient = $arrayCoefficient[$i];
    $price = averagePrice($coefficient, $optPrice, $retailPrice);

    isset($product['error_message']) ? $error = $product['error_message']: $error ='';
    $description = $product['result']['description'];

    empty($description) ? $description = $product['result']['brief_description'] : $description;
    $delTags = strip_tags($description);
    $links = getLinksPhoto($id, $sid);

    if($i == 0) {
        $spreadsheet = IOFactory::load('../import-file/example-table.xlsx');
    } else {
        $spreadsheet = IOFactory::load('../import-file/Import-Products.xlsx');
   }
    $newRow = $lastRow;
    $sheet = $spreadsheet->getActiveSheet();

    $data = [
        [$article, $name, $name, "$article, $arrayCode[$i]", "$article, $arrayCode[$i]", $delTags, $delTags,
            $price, "UAH", "шт.",  $links, $avaliable, $article, "Загальні характеристики", '', $briefDescription]
    ];

    foreach ($data as $rowIndex => $rowData) {
        foreach ($rowData as $columnIndex => $cellData) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1);
            $cellCoordinate = $columnLetter . ($lastRow);
            $sheet->setCellValue($cellCoordinate, $cellData);
        }
        $lastRow++;
    }

    try {
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        //$writer = new Xlsx($spreadsheet);
        $writer->save('../import-file/Import-Products.xlsx');
    } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
        die("Ошибка записи: " . $e);
    }
    $i++;
}
echo "Дані успішно додані в базу";
//Ліквідація сесії SID
$logout = new \app\CurlRequest("http://api.brain.com.ua/logout/$sid");
$logout->post();

function getLinksPhoto($id, $sid ): string // отримує лінки на фото, не більше 10шт.
{
    $i = 1;
    $links = "";
    $request = new \app\CurlRequest("http://api.brain.com.ua/product_pictures/$id/$sid");
    $response = $request->get([]);
    $arrayLinks= json_decode($response, true);
    foreach ($arrayLinks['result'] as $item) {
        foreach ($item as $key => $value) {
            if($key === "large_image" && $i < 10) {
                $links .= $value . ', ';
                $i++;

            }
        }
    }
    return $links;
}

function getProduct($code, $sid): array // отримання информації о продукції
{
    $request = new \app\CurlRequest("http://api.brain.com.ua/product/product_code/$code/$sid?lang=ua");
    $response = $request->get([]);
    return json_decode($response, true);
}

function averagePrice($coefficient, $optPrice, $retailPrice): int // отримання ціни
{
    $price = $optPrice + (($retailPrice - $optPrice) * $coefficient);
    return (int) $price;
}

$end = microtime(true);
$executionTime = $end - $start;
echo "\nСкрипт выполнен за: " . round($executionTime) . " секунд.";





