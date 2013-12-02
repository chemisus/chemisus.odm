<?php

namespace Test\Snuggie;


use PHPUnit_Framework_TestCase;
use Snuggie\Response;

class ResponseTest extends PHPUnit_Framework_TestCase
{

    public function testFactory()
    {
        $data = 'HTTP/1.1 200 OK' . "\r\n" .
            'Date: Thu, 17 Aug 2006 05:39:28 +0000GMT' . "\r\n" .
            'Content-Length: 67' . "\r\n" .
            'Content-Type: application/json' . "\r\n" .
            'Connection: close' . "\r\n" .
            '' . "\r\n" .
            '{"ok": true}'. "\n";

        $response = Response::Factory($data);

        $this->assertEquals('HTTP', $response->protocol());
        $this->assertEquals('1.1', $response->version());
        $this->assertEquals('200', $response->status());
        $this->assertEquals('OK', $response->message());
        $this->assertEquals(
            [
                'Date'           => 'Thu, 17 Aug 2006 05:39:28 +0000GMT',
                'Content-Length' => '67',
                'Content-Type'   => 'application/json',
                'Connection'     => 'close',
            ],
            $response->headers()
        );
        $this->assertEquals('{"ok": true}'. "\n", $response->body());
    }
}
