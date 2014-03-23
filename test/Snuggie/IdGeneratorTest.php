<?php

namespace Snuggie;

use Mockery;

/**
 * Test data is pulled from http://docs.couchdb.org/en/latest/api/server/common.html#uuids
 */
class IdGeneratorTest extends TestCase
{
    public function testGenerateIds()
    {
        $rf = new ResponseFactory();
        $op = new IdGenerator($rf);
        $connection = Mockery::mock('Snuggie\Connection');
        $expect = ["75480ca477454894678e22eec6002413", "75480ca477454894678e22eec600250b", "75480ca477454894678e22eec6002c41", "75480ca477454894678e22eec6003b90", "75480ca477454894678e22eec6003fca", "75480ca477454894678e22eec6004bef", "75480ca477454894678e22eec600528f", "75480ca477454894678e22eec6005e0b", "75480ca477454894678e22eec6006158", "75480ca477454894678e22eec6006161"];
        $response = 'HTTP/1.1 200 OK' . "\n" .
            'Content-Length: 362' . "\n" .
            'Content-Type: application/json' . "\n" .
            'Date: Sat, 10 Aug 2013 11:46:25 GMT' . "\n" .
            'ETag: "DGRWWQFLUDWN5MRKSLKQ425XV"' . "\n" .
            'Expires: Fri, 01 Jan 1990 00:00:00 GMT' . "\n" .
            'Pragma: no-cache' . "\n" .
            'Server: CouchDB (Erlang/OTP)' . "\n" .
            '' . "\n" .
            '{"uuids": ["75480ca477454894678e22eec6002413","75480ca477454894678e22eec600250b","75480ca477454894678e22eec6002c41","75480ca477454894678e22eec6003b90","75480ca477454894678e22eec6003fca","75480ca477454894678e22eec6004bef","75480ca477454894678e22eec600528f","75480ca477454894678e22eec6005e0b","75480ca477454894678e22eec6006158","75480ca477454894678e22eec6006161"]}';

        $connection->shouldReceive('request')->with('GET', '_uuids?count=10')->once()->andReturn($response);

        $actual = $op->generateIds($connection, 10);

        $this->assertEquals($expect, $actual);
    }

    public function testGenerateId()
    {
        $rf = new ResponseFactory();
        $op = new IdGenerator($rf);
        $connection = Mockery::mock('Snuggie\Connection');
        $expect = "75480ca477454894678e22eec6002413";
        $response = 'HTTP/1.1 200 OK' . "\n" .
            'Content-Length: 362' . "\n" .
            'Content-Type: application/json' . "\n" .
            'Date: Sat, 10 Aug 2013 11:46:25 GMT' . "\n" .
            'ETag: "DGRWWQFLUDWN5MRKSLKQ425XV"' . "\n" .
            'Expires: Fri, 01 Jan 1990 00:00:00 GMT' . "\n" .
            'Pragma: no-cache' . "\n" .
            'Server: CouchDB (Erlang/OTP)' . "\n" .
            '' . "\n" .
            '{"uuids": ["75480ca477454894678e22eec6002413"]}';

        $connection->shouldReceive('request')->with('GET', '_uuids?count=1')->once()->andReturn($response);

        $actual = $op->generateId($connection);

        $this->assertEquals($expect, $actual);
    }
}
