<?php

namespace Test\Snuggie;

use PHPUnit_Framework_TestCase;
use Snuggie\Connection;
use Snuggie\Database;
use Snuggie\Response;
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

        $this->connection->put('/db-test');
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->connection->delete('/db-test');
    }

    public function testCreateDocument()
    {
        $value = [
            'a' => 'A',
            'b' => 'B',
        ];

        $response = Response::Factory($this->database->createDocument($value));

        $this->assertStringStartsWith('{"ok":true,"id":"', $response->body());
    }

    public function testInsertDocument()
    {
        $value = [
            'a' => 'A',
            'b' => 'B',
        ];

        $response = Response::Factory($this->database->insertDocument('test-doc', $value));

        $this->assertStringStartsWith('{"ok":true,"id":"', $response->body());
    }

    public function testHasDocument()
    {
        $value = [
            'a' => 'A',
            'b' => 'B',
        ];

        $this->database->insertDocument('test-doc', $value);
        $this->assertTrue($this->database->hasDocument('test-doc'));
    }

    public function testNotHasDocument()
    {
        $this->assertFalse($this->database->hasDocument('test-doc'));
    }

    /**
     * @depends testInsertDocument
     * @depends testNotHasDocument
     */
    public function testDeleteDocument()
    {
        $value = [
            'a' => 'A',
            'b' => 'B',
        ];

        $response = Response::Factory($this->database->insertDocument('test-doc', $value));

        $value = json_decode($response->body());

        $this->database->deleteDocument('test-doc', $value->rev);
        $this->assertFalse($this->database->hasDocument('test-doc'));
    }
}
