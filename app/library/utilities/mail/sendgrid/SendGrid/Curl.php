<?php

namespace SendGrid;

class Curl
{
    public $ch;
    private $base_url;

    public function __construct($base_url)
    {
        $this->ch = curl_init();
        $this->base_url = $base_url;

        echo("\n\n-------\n\n");
    }

    public function post($endpoint, $headers = null, $fields = null)
    {
        curl_setopt($this->ch, CURLOPT_URL, $this->base_url.$endpoint);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 1);
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        
        $response = curl_exec ($this->ch);

        $http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        curl_close ($this->ch);

        return array('status_code' => $http_code,
                        'headers' => $header,
                        'body' => $body,
                        'json' => '');
    }
}
