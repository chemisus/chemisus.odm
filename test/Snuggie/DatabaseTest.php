<?php

namespace Test\Snuggie;

use PHPUnit_Framework_TestCase;
use Snuggie\Connection;
use Snuggie\Database;
use Snuggie\Server;

class DatabaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var Database
     */
    private $database;

    public function setUp()
    {
        parent::setUp();

        $host             = 'localhost';
        $port             = 5984;
        $this->connection = new Connection($host, $port);
        $this->server     = new Server($this->connection);
        $this->database   = new Database($this->server, 'db-test');

        $this->server->createDatabase('db-test');
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->connection->delete('db-test', json_encode(null));
    }

}
