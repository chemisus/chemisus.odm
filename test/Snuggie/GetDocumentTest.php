<?php

namespace Snuggie;

use Mockery;

class GetDocumentTest extends TestCase
{
    public function testGetDocument()
    {
        $rf = new ResponseFactory();
        $op = new GetDocument($rf);
        $connection = Mockery::mock('Snuggie\Connection');
        $database = 'db_file';
        $id = 'f059893fa0ad5ef21d2d2ef53e007d4d';
        $response = 'HTTP/1.1 200 OK' . "\n" .
            'Server: CouchDB/1.3.1 (Erlang OTP/R15B03)' . "\n" .
            'ETag: "1-ba9467e0eba51ba5d9284e4ac0ad6794"' . "\n" .
            'Date: Sun, 23 Mar 2014 17:16:32 GMT' . "\n" .
            'Content-Type: text/plain; charset=utf-8' . "\n" .
            'Content-Length: 649' . "\n" .
            'Cache-Control: must-revalidate' . "\n" .
            '' . "\n" .
            '{"_id":"f059893fa0ad5ef21d2d2ef53e007d4d","_rev":"1-ba9467e0eba51ba5d9284e4ac0ad6794","type":"key","key":"c3NoLXJzYSBBQUFBQjNOemFDMXljMkVBQUFBREFRQUJBQUFCQVFEUkJBejhFbkdGalRleHhsbjBNM01ybndOTkRrdElGNDl3QlNqdWVqZjU4ek44YUdGREJXZHVWaUxUcldGZDhEYVdmY2tqSHlnNVpyWGczKzF4cHZNVUdEbWxCclEyWndoeWJSN0UrUUZsRVE3MDhVUDdYRGNmbXZXSzdpR25sbmhlaEpRWG9qMVI0WjUwWnVkeDJ6WitxcVdkcXo1TEZma2plVWROd2ZRRVhsd2t4TXQ0dUZxZDQxOGlkRjkzcWJRRDJncFVtbHVyV3BtTEw3U242WDZNazdka28wMVRiWnl0N1pCZ2EvWEdMWkxxRHF6SkV5K3poQ1NBamVUK01SRWFpbnl2YnA2QndUYTBZYjRJUnJWV2R4RFFRWk5uQ2pYMHY0d3J3aGdkKzlFQ1RnNXJlK3hxV05TVXFQK1dYaFJEWkJyMCtCRWZrYkJmRTExVGxOdVogY2hlbWlzdXNAcGFyYWtlZXQubG9jYWwK"}';

        $connection->shouldReceive('request')->with('GET', $database . '/' . $id)->once()->andReturn($response);

        $document = $op->getDocument($connection, $database, $id);

        $this->assertEquals($id, $document->_id);
    }

    public function testGetDocumentError()
    {
        $rf = new ResponseFactory();
        $op = new GetDocument($rf);
        $connection = Mockery::mock('Snuggie\Connection');
        $database = 'db_file';
        $id = 'f059893fa0ad5ef21d2d2ef53e007d4';
        $response = 'HTTP/1.1 404 Object Not Found' . "\n" .
            'Server: CouchDB/1.3.1 (Erlang OTP/R15B03)' . "\n" .
            'Date: Sun, 23 Mar 2014 17:26:30 GMT' . "\n" .
            'Content-Type: text/plain; charset=utf-8' . "\n" .
            'Content-Length: 41' . "\n" .
            'Cache-Control: must-revalidate' . "\n" .
            '' . "\n" .
            '{"error":"not_found","reason":"missing"}';

        $connection->shouldReceive('request')->with('GET', $database . '/' . $id)->once()->andReturn($response);

        $this->shouldThrow('Snuggie\DocumentNotFoundException', function () use ($connection, $database, $id, $op) {
            $op->getDocument($connection, $database, $id);
        });
    }
}