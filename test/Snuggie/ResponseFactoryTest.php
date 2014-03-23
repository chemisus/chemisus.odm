<?php

namespace Snuggie;

class ResponseFactoryTest extends TestCase
{
    private $value;

    public function setUp()
    {
        parent::setUp();

        $this->value = 'HTTP/1.1 200 OK' . "\r\n" .
            'Server: CouchDB/1.3.1 (Erlang OTP/R15B03)' . "\r\n" .
            'Date: Sun, 23 Mar 2014 15:51:30 GMT' . "\r\n" .
            'Content-Type: text/plain; charset=utf-8' . "\r\n" .
            'Content-Length: 131' . "\r\n" .
            'Cache-Control: must-revalidate' . "\r\n" .
            '' . "\r\n" .
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

    public function testGetDocument()
    {
        $value = 'HTTP/1.1 200 OK' . "\r\n" .
            'Server: CouchDB/1.3.1 (Erlang OTP/R15B03)' . "\r\n" .
            'ETag: "7-9b82726d3c5ef5d5d95ae33ec59ddd06"' . "\r\n" .
            'Date: Sun, 23 Mar 2014 21:09:51 GMT' . "\r\n" .
            'Content-Type: text/plain; charset=utf-8' . "\r\n" .
            'Content-Length: 456' . "\r\n" .
            'Cache-Control: must-revalidate' . "\r\n" .
            '' . "\r\n" .
            '{"_id":"doc","_rev":"7-9b82726d3c5ef5d5d95ae33ec59ddd06","_attachments":{"key":{"content_type":"application/octet-stream","revpos":7,"digest":"md5-1B2M2Y8AsgTpgAmY7PhCfg==","length":0,"stub":true},"b":{"content_type":"application/octet-stream","revpos":6,"digest":"md5-fcbr7InXnOCoyr/rRJ3DtA==","length":405,"stub":true},"a":{"content_type":"application/x-www-form-urlencoded","revpos":3,"digest":"md5-fcbr7InXnOCoyr/rRJ3DtA==","length":405,"stub":true}}}';

        $expect = 'doc';
        $factory = new ResponseFactory();
        $actual = json_decode($factory->make($value)->body())->_id;
        $this->assertEquals($expect, $actual);
    }

}