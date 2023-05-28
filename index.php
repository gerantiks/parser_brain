<?php

require_once('InformationApi.php');

$url = "http://api.brain.com.ua/auth";

$data = array(
    'login' => $login,
    'password' => $password
);

$curl = curl_init();
$options = array(CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_HEADER => true);

curl_setopt_array($curl, $options);
$content = curl_exec($curl);

if ($content === false ) {
    $error = curl_error($curl);
    echo $error;
} else {
    echo $content;
}

curl_close($curl);