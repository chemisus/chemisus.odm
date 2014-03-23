<?php

namespace Snuggie;

class ResponseFactory
{
    public function make($value)
    {
        $protocol = null;
        $status = null;
        $message = null;
        $headers = [];
        $body = null;

        $offset = 0;

        $matches = [];

        if (preg_match('/(\S+) (\d+)(.*?)\r?\n/', $value, $matches, null, $offset)) {
            $protocol = $matches[1];
            $status = $matches[2];
            $message = trim($matches[3]);
            $offset += strlen($matches[0]);
        }

        while (preg_match('/(.*?):(.*?)\r?\n/', $value, $matches, null, $offset)) {
            if ($matches[0][0] === '{') {
                break;
            }

            $headers[trim($matches[1])] = trim($matches[2]);
            $offset += strlen($matches[0]);
        }

        if (preg_match('/(\r?\n)/', $value, $matches, null, $offset)) {
            $offset += strlen($matches[0]);
        }

        $body = substr($value, $offset);

        return new Response($protocol, $status, $message, $headers, $body);
    }
}