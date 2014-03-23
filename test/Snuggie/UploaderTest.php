<?php

namespace Snuggie;

class UploaderTest extends TestCase
{
    public function testUpload()
    {
        $connection = new Connection('http://localhost:5984');
        $uploader = new Uploader($connection);
        $rf = new ResponseFactory();
        $get = new GetDocument($rf);

        $doc = $get->getDocument($connection, 'test', 'doc');

        $uploader->upload('PUT', 'test/' . $doc->_id . '/keya?rev=' . $doc->_rev, 'key');
    }
}