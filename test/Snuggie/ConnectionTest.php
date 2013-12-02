<?php

namespace Test\Snuggie;

use PHPUnit_Framework_TestCase;
use Snuggie\Connection;

class ConnectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    private $connection;

    public function setUp()
    {
        parent::setUp();

        $host             = 'localhost';
        $port             = 5984;
        $this->connection = new Connection($host, $port);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->connection->delete('db-test', json_encode(null));
    }

    public function testGet()
    {
        $url    = '/';
        $actual = $this->connection->get($url);
        $this->assertNotEquals(false, $actual);
    }

    /**
     * @depends testGet
     */
    public function testPut()
    {
        $url    = '/test-db';
        $json = $this->connection->put($url);
        $actual = json_decode($json);
        $this->assertTrue($actual->ok);
    }

    /**
     * @depends testPut
     */
    public function testPost()
    {
        $object = new \stdClass();
        $object->a = 'hi';

        $url    = '/test-db';
        $json = $this->connection->post($url, json_encode($object));
        $actual = json_decode($json);
        $this->assertTrue($actual->ok);
    }

    /**
     * @depends testPost
     */
    public function testDelete()
    {
        $url    = '/test-db';
        $json = $this->connection->delete($url, json_encode(null));
        $actual = json_decode($json);
        $this->assertTrue($actual->ok);
    }
}
