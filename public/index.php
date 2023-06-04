<?php

require_once('../private/information_api.php');
require_once('../vendor/autoload.php');
global $login, $password;

$request = new \app\CurlRequest('http://api.brain.com.ua/auth');

$params = [
    'login' => $login,
    'password' => $password
];

try {
    $response = $request->post($params);
    print_r($response);
} catch (Exception $e) {
    echo $e->getMessage();
}