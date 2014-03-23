<?php

namespace Snuggie;

class ResponseFactoryTest extends TestCase
{
    private $value;

    public function setUp()
    {
        parent::setUp();

        $this->value = 'HTTP/1.1 200 OK' . "\n" .
            'Server: CouchDB/1.3.1 (Erlang OTP/R15B03)' . "\n" .
            'Date: Sun, 23 Mar 2014 15:51:30 GMT' . "\n" .
            'Content-Type: text/plain; charset=utf-8' . "\n" .
            'Content-Length: 131' . "\n" .
            'Cache-Control: must-revalidate' . "\n" .
            '' . "\n" .
            '{"couchdb":"Welcome","uuid":"053111aad86806b3c589def32f65b74b","version":"1.3.1","vendor":{"version":"1.3.1-1","name":"Homebrew"}}';
    }

    public function testMakeReturnsResponse()
    {
        $factory = new ResponseFactory();
        $result = $factory->make($this->value);
        $this->assertInstanceOf("Snuggie\Response", $result);
    }

    public function testParseProtocol()
    {
        $expect = "HTTP/1.1";
        $factory = new ResponseFactory();
        $actual = $factory->make($this->value)->protocol();
        $this->assertEquals($expect, $actual);
    }

    public function testParseStatus()
    {
        $expect = "200";
        $factory = new ResponseFactory();
        $actual = $factory->make($this->value)->status();
        $this->assertEquals($expect, $actual);
    }

    public function testParseMessage()
    {
        $expect = "OK";
        $factory = new ResponseFactory();
        $actual = $factory->make($this->value)->message();
        $this->assertEquals($expect, $actual);
    }

    public function testParseHeaders()
    {
        $expect = [
            'Server' => 'CouchDB/1.3.1 (Erlang OTP/R15B03)',
            'Date' => 'Sun, 23 Mar 2014 15:51:30 GMT',
            'Content-Type' => 'text/plain; charset=utf-8',
            'Content-Length' => '131',
            'Cache-Control' => 'must-revalidate',
        ];
        $factory = new ResponseFactory();
        $actual = $factory->make($this->value)->headers();
        $this->assertEquals($expect, $actual);
    }

    public function testParseBody()
    {
        $expect = '{"couchdb":"Welcome","uuid":"053111aad86806b3c589def32f65b74b","version":"1.3.1","vendor":{"version":"1.3.1-1","name":"Homebrew"}}';
        $factory = new ResponseFactory();
        $actual = $factory->make($this->value)->body();
        $this->assertEquals($expect, $actual);
    }

}