<?php

namespace Snuggie;

class Connection
{
    private $base;

    public function __construct($base)
    {
        $this->base = $base;
    }

    public function path($path)
    {
        return $this->base . '/' . trim($path, '/');
    }

    public function request($method, $path)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->path($path));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        $rf = new ResponseFactory();

        return $response;
    }
}