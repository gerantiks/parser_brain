<?php

namespace app;
class CurlRequest
{
    private string $url;
    private array $option;

    public function __construct($url) {
        $this -> url = $url;
        $this -> option = [];

    }

    public function setOption($option, $value) {
        $this->option[$option] = $value;
    }

    public function get($params):string  {
        $url = $this->buildUrl($params);
        $this->option[CURLOPT_HTTPGET] = true;
        return $this->sendRequest($url);
    }

    public function post($params = []):string  {
        $this -> option[CURLOPT_POST] = true;
        $this -> option[CURLOPT_POSTFIELDS] = http_build_query($params);
        return $this->sendRequest($this-> url);
    }

    private function buildUrl($params):string {
        if(!empty($params)) {
            $query = http_build_query($params);
            return $this->url.$query;
        }
        return $this->url;
    }

    private function sendRequest($url):string {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        foreach ($this->option as $option => $value) {
            curl_setopt($curl, $option, $value);
        }
        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if($error) {
            throw new \Exception('Error: ' . $error);
        }
        return $response;

    }
}