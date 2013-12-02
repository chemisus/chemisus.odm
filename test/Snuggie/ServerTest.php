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

    public function testCreateDatabase()
    {
        $this->server->createDatabase('test-db');
    }

    /**
     * @depends testCreateDatabase
     */
    public function testHasDatabase()
    {
        $this->server->hasDatabase('test-db');
    }

    /**
     * @depends testHasDatabase
     */
    public function testDeleteDatabase()
    {
        $this->server->deleteDatabase('test-db');
    }
}
