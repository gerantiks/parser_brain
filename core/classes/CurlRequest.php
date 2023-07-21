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

    public function get($params = [], $decode = 0)  { //decode 0 возвращает json;  1 - декодированный массив
        $url = $this->buildUrl($params);
        $this->option[CURLOPT_HTTPGET] = true;
        if($decode === 0) {
            $result = $this->sendRequest($this-> url);
        } else {
            $result = $this->sendRequest($this-> url);
            $result = json_decode($result, true);
        }
        return $result;

    }

    public function post($params = [], $decode = 0) {
        $this -> option[CURLOPT_POST] = true;
        $this -> option[CURLOPT_POSTFIELDS] = http_build_query($params);
        if($decode === 0) {
            $result = $this->sendRequest($this-> url);
        } else {
            $result = $this->sendRequest($this-> url);
            $result = json_decode($result, true);
        }
        return $result;
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

    public function MultiRequests($urls, $decode = 0)
    {
        $result = [];
        $multiHandle = curl_multi_init();
        $curlHandles = [];
        foreach ($urls as $url) {
            $handle = curl_init($url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($multiHandle, $handle);
            $curlHandles[] = $handle;
        }
    do {
        $status = curl_multi_exec($multiHandle, $active);
    } while ($status === CURLM_CALL_MULTI_PERFORM || $active);
        foreach ($curlHandles as $handle) {
            $item= curl_multi_getcontent($handle);
            if($decode != 0) {
                $result[] = json_decode($item, true);
            } else {
                $result[] = $item;
            }
            curl_multi_remove_handle($multiHandle, $handle);
            curl_close($handle);
        }

    curl_multi_close($multiHandle);
    return $result;
    }

}