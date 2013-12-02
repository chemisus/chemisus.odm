<?php

namespace Snuggie;

class Response
{
    public static function Factory($response)
    {
        $data         = explode("\r\n\r\n", $response, 2);
        $header       = array_shift($data);
        $body         = array_shift($data);
        $header_lines = explode("\r\n", $header);
        $information  = array_shift($header_lines);
        $details      = [];

        preg_match('/([^\/]+)\/(\S+)\s+(\S+)\s+(.*?)\r?\n?$/', $information, $details);

        $protocol = $details[1];
        $version  = $details[2];
        $status   = $details[3];
        $message  = $details[4];

        $headers = [];
        foreach ($header_lines as $line) {
            $value = explode(': ', $line);

            $headers[$value[0]] = $value[1];
        }

        return new Response($protocol, $version, $status, $message, $headers, $body);
    }

    private $protocol;
    private $version;
    private $status;
    private $message;
    private $headers;
    private $body;

    public function __construct($protocol, $version, $status, $message, $headers, $body)
    {
        $this->protocol = $protocol;
        $this->version  = $version;
        $this->status   = $status;
        $this->message  = $message;
        $this->headers  = $headers;
        $this->body     = $body;
    }

    public function protocol()
    {
        return $this->protocol;
    }

    public function version()
    {
        return $this->version;
    }

    public function status()
    {
        return $this->status;
    }

    public function message()
    {
        return $this->message;
    }

    public function headers()
    {
        return $this->headers;
    }

    public function body()
    {
        return $this->body;
    }
}