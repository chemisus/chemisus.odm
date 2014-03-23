<?php

namespace Snuggie;

use Mockery;

/**
 * Test data pulled from http://docs.couchdb.org/en/latest/api/document/attachments.html#put--db-docid-attname
 */
class CreateAttachmentTest extends TestCase
{
    public function testCreateAttachment()
    {
        $rf = new ResponseFactory();
        $op = new CreateAttachment($rf);
        $connection = Mockery::mock('Snuggie\Uploader');
        $database = 'recipes';
        $id = 'SpaghettiWithMeatballs';
        $revision = '1-917fa2381192822767f010b95b45325b';
        $name = 'recipe.txt';
        $file = 'recipe.txt';
        $expect = '2-ce91aed0129be8f9b0f650a2edcfd0a4';
        $response = 'HTTP/1.1 201 Created' . "\n" .
            'Cache-Control: must-revalidate' . "\n" .
            'Content-Length: 85' . "\n" .
            'Content-Type: application/json' . "\n" .
            'Date: Thu, 15 Aug 2013 12:38:04 GMT' . "\n" .
            'ETag: "2-ce91aed0129be8f9b0f650a2edcfd0a4"' . "\n" .
            'Location: http://localhost:5984/recipes/SpaghettiWithMeatballs/recipe.txt' . "\n" .
            'Server: CouchDB (Erlang/OTP)' . "\n" .
            '' . "\n" .
            '{"id": "SpaghettiWithMeatballs","ok": true,"rev": "2-ce91aed0129be8f9b0f650a2edcfd0a4"}';

        $connection->shouldReceive('upload')->with('PUT', $database . '/' . $id . '/' . $name . '?rev=' . $revision, $file)->once()->andReturn($response);

        $document = $op->createAttachment($connection, $database, $id, $revision, $name, $file);

        $this->assertEquals($expect, $document->rev);
    }


    public function testCreateAttachmentFail()
    {
        $rf = new ResponseFactory();
        $op = new CreateAttachment($rf);
        $connection = Mockery::mock('Snuggie\Uploader');
        $database = 'recipes';
        $id = 'SpaghettiWithMeatballs';
        $revision = '1-917fa2381192822767f010b95b45325b';
        $name = 'recipe.txt';
        $file = 'recipe.txt';
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

        $connection->shouldReceive('upload')->with('PUT', $database . '/' . $id . '/' . $name . '?rev=' . $revision, $file)->once()->andReturn($response);

        $this->shouldThrow('Snuggie\AttachmentCreationException', function () use ($connection, $database, $id, $op, $revision, $name, $file) {
            $op->createAttachment($connection, $database, $id, $revision, $name, $file);
        });
    }
}
