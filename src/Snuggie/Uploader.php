<?php

namespace Snuggie;

class Uploader
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function upload($method, $remote_path, $local_path)
    {
        $file = fopen($local_path, "rb");

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->connection->path($remote_path));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_PUT, 1);
        curl_setopt($ch, CURLOPT_INFILE, $file);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($local_path));

        $response = curl_exec($ch);

        curl_close($ch);
        fclose($file);

        return $response;
    }
}
