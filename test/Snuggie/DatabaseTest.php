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

        $json = json_decode($response->body());

        $this->database->deleteDocument('test-doc', $json->rev);
        $this->assertFalse($this->database->hasDocument('test-doc'));
    }

    /**
     * @depends testInsertDocument
     */
    public function testGetDocument()
    {
        $value = [
            'a' => 'A',
            'b' => 'B',
        ];

        $response = Response::Factory($this->database->insertDocument('test-doc', $value));

        $json = json_decode($response->body());

        $actual = $this->database->getDocument('test-doc');

        $this->assertEquals('A', $actual->a);
    }

    /**
     * @depends testInsertDocument
     */
    public function testUpdateDocument()
    {
        $value = [
            'a' => 'A',
            'b' => 'B',
        ];

        $response = Response::Factory($this->database->insertDocument('test-doc', $value));

        $value = json_decode($response->body());

        $updated = [
            'c' => 'C',
            'd' => 'D',
        ];

        $response = Response::Factory($this->database->updateDocument('test-doc', $value->rev, $updated));

        $this->assertTrue(strpos($response->body(), '"rev":"2-') !== false);
    }

    public function testInsertView()
    {
        $view = [
            'views' => [
                'all' => [
                    'map' => 'function (doc) {emit(null, doc._id);}'
                ]
            ]
        ];

        $this->database->insertView('test-view', $view);

        $this->assertTrue($this->database->hasDocument('_design/test-view'));
    }

    /**
     * @depends testInsertView
     */
    public function testHasView()
    {
        $view = [
            'views' => [
                'all' => [
                    'map' => 'function (doc) {emit(null, doc._id);}'
                ]
            ]
        ];

        $this->database->insertView('test-view', $view);

        $this->assertTrue($this->database->hasView('test-view'));
    }

    public function testHasNotView()
    {
        $this->assertFalse($this->database->hasView('asdfasdfasdf'));
    }

    public function testRunView()
    {
        $view = [
            'views' => [
                'all' => [
                    'map' => 'function (doc) {emit(null, doc._id);}'
                ]
            ]
        ];

        $value = [
            'a' => 'A',
            'b' => 'B',
        ];

        $this->database->insertDocument('test-doc1', $value);
        $this->database->insertDocument('test-doc2', $value);
        $this->database->insertDocument('test-doc3', $value);
        $this->database->insertView('test-view', $view);
        $response = $this->database->runView('test-view', 'all');

        $this->assertEquals(3, $response->total_rows);
    }
}
