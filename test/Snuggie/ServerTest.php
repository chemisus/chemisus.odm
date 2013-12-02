<?php

namespace Test\Snuggie;


use PHPUnit_Framework_TestCase;
use Snuggie\Connection;
use Snuggie\Server;

class ServerTest extends PHPUnit_Framework_TestCase
{
    private $connection;

    /**
     * @var Server
     */
    private $server;

    public function setUp()
    {
        parent::setUp();

        $host             = 'localhost';
        $port             = 5984;
        $this->connection = new Connection($host, $port);
        $this->server     = new Server($this->connection);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->connection->delete('/db-test');
    }

    public function testCreateDatabase()
    {
        $this->server->createDatabase('test-db');
    }

    /**
     * @depends testCreateDatabase
     */
    public function testHasDatabase()
    {
        $this->server->createDatabase('test-db');
        $this->assertTrue($this->server->hasDatabase('test-db'));
    }

    /**
     * @depends testCreateDatabase
     * @depends testHasDatabase
     */
    public function testDeleteDatabase()
    {
        $this->server->createDatabase('test-db');
        $this->server->deleteDatabase('test-db');
        $this->assertFalse($this->server->hasDatabase('test-db'));
    }
}
