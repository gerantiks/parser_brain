<?php
require_once('../private/information_api.php');
require_once('../vendor/autoload.php');

$error = '';

$request = new \app\CurlRequest("http://api.brain.com.ua/product/product_code/$codeProduct/$sid?lang=ua");

$response = $request->get([]);
$decodeResponse = json_decode($response, true);
isset($decodeResponse['error_message']) ? $error = $decodeResponse['error_message']: $error ='';
print_r($decodeResponse);

$dataProduct = [
    'positionId' => 0,
    'name' => $decodeResponse['result']['name'],
    'avaliable' => $decodeResponse['result']['self_delivery'],
    'article' => $decodeResponse['result']['articul'],
    'briefDescription' => $decodeResponse['result']['brief_description'],
    'description' => $decodeResponse['result']['description'],
    'links' => $decodeResponse['result']['medium_image']
];


