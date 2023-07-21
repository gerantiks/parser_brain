<?php
function authorization($client)
{
    $response = $client->request('POST', URL_BRAIN . '/auth', [
        'form_params' => [
            'login'    => LOGIN,
            'password' => PASSWORD,
        ],
    ]);
    $response = json_decode($response->getBody(), true);
    $response['status'] === 1 ? $result = $response['result'] : $result = die('Проблема з авторизацією');
    return $result;
}

function logout($client, $sid)
{
    $response = $client->request('POST', URL_BRAIN . "/logout/$sid");
    return json_decode($response->getBody(), true);
}


function getData(object $client, string $url, int $code = 200): array | string
{
    $response = $client->request('GET', $url);
    if ($response->getStatusCode() == $code) {
        return json_decode($response->getBody(), true);
    } else {
        return '';
    }
}

function stripTagsText($data): string
{
    return strip_tags($data);
}

function getlistLinks(array $arrayLinks, int $amount): string
{
    $i = 1;
    $links = "";
    foreach ($arrayLinks as $item) {
        foreach ($item as $key => $value) {
            if($key === "large_image" && $i++ < $amount) {
                $links .= $value . ', ';
            }
        }
    }
    return $links;
}

function getAveragePrice(int $opt, int $retail, $coefficient): int
{
    empty($coefficient) ?? $coefficient = 0.5;
    return round($opt + (($retail - $opt) * $coefficient), -1);
}

