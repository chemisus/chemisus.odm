<?php

namespace Snuggie;

class Response
{
    private $protocol;
    private $status;
    private $message;
    private $headers;
    private $body;

    public function __construct($protocol, $status, $message, $headers, $body)
    {
        $this->protocol = $protocol;
        $this->status = $status;
        $this->message = $message;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function protocol()
    {
        return $this->protocol;
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

    public function success()
    {
        return $this->status() === '200';
    }
}