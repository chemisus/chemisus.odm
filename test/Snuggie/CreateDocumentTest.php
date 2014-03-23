<?php

namespace Snuggie;

use Mockery;

/**
 * Test data pulled from http://docs.couchdb.org/en/latest/api/document/common.html#put--db-docid
 */
class CreateDocumentTest extends TestCase
{
    public function testCreateDocument()
    {
        $rf = new ResponseFactory();
        $op = new CreateDocument($rf);
        $connection = Mockery::mock('Snuggie\Connection');
        $database = 'recipes';
        $id = 'SpaghettiWithMeatballs';
        $expect = '1-917fa2381192822767f010b95b45325b';
        $body = '{"description": "An Italian-American dish that usually consists of spaghetti, tomato sauce and meatballs.","ingredients": ["spaghetti","tomato sauce","meatballs"],"name": "Spaghetti with meatballs"}';
        $response = 'HTTP/1.1 201 Created' . "\n" .
            'Cache-Control: must-revalidate' . "\n" .
            'Content-Length: 85' . "\n" .
            'Content-Type: application/json' . "\n" .
            'Date: Wed, 14 Aug 2013 20:31:39 GMT' . "\n" .
            'ETag: "1-917fa2381192822767f010b95b45325b"' . "\n" .
            'Location: http://localhost:5984/recipes/SpaghettiWithMeatballs' . "\n" .
            'Server: CouchDB (Erlang/OTP)' . "\n" .
            '' . "\n" .
            '{"id": "SpaghettiWithMeatballs","ok": true,"rev": "1-917fa2381192822767f010b95b45325b"}';

        $connection->shouldReceive('request')->with('PUT', $database . '/' . $id, $body)->once()->andReturn($response);

        $document = $op->createDocument($connection, $database, $id, $body);

        $this->assertEquals($expect, $document->rev);
    }

    public function testCreateDocumentFail()
    {
        $rf = new ResponseFactory();
        $op = new CreateDocument($rf);
        $connection = Mockery::mock('Snuggie\Connection');
        $database = 'recipes';
        $id = 'SpaghettiWithMeatballs';
        $body = '{"description": "An Italian-American dish that usually consists of spaghetti, tomato sauce and meatballs.","ingredients": ["spaghetti","tomato sauce","meatballs"],"name": "Spaghetti with meatballs"}';
        $response = 'HTTP/1.1 400 Bad Request' . "\n" .
            'Cache-Control: must-revalidate' . "\n" .
            'Content-Length: 85' . "\n" .
            'Content-Type: application/json' . "\n" .
            'Date: Wed, 14 Aug 2013 20:31:39 GMT' . "\n" .
            'ETag: "1-917fa2381192822767f010b95b45325b"' . "\n" .
            'Location: http://localhost:5984/recipes/SpaghettiWithMeatballs' . "\n" .
            'Server: CouchDB (Erlang/OTP)' . "\n" .
            '' . "\n" .
            '{}';

        $connection->shouldReceive('request')->with('PUT', $database . '/' . $id, $body)->once()->andReturn($response);

        $this->shouldThrow('Snuggie\DocumentCreationException', function () use ($connection, $database, $id, $op, $body) {
            $op->createDocument($connection, $database, $id, $body);
        });
    }
}
