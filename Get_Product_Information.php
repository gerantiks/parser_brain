<?php
require_once('InformationApi.php');
$url = "http://api.brain.com.ua/product/product_code/$codeProduct/$sid?lang=ua";


$curl = curl_init();

$option = array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => 1);

curl_setopt_array($curl, $option);

$content = curl_exec($curl);

curl_close($curl);

$contentDecode = json_decode($content, true);
$error = 0;
$contentDecode['status'] === 0 ? $error = 1 : print_r($contentDecode);

//Запит на отримання посилання на фото
if($error === 0) {
    $productID = $contentDecode['result']['productID'];

    $urlPhoto = "http://api.brain.com.ua/product_pictures/$productID/$sid";
    $curl = curl_init();

    $optionPhoto = array(
        CURLOPT_URL => $urlPhoto,
        CURLOPT_RETURNTRANSFER => 1);

    curl_setopt_array($curl, $optionPhoto);

    $linksPhoto = curl_exec($curl);

    curl_close($curl);

    $decodeLinksPhoto = json_decode($linksPhoto, true);

    print_r($decodeLinksPhoto);
} else {
    echo 'Виникла помилка ! Перевірте правильність ведення коду товару';
}
