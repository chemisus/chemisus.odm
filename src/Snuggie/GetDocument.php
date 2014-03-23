<?php

namespace Snuggie;

use Exception;

class GetDocument
{
    private $response_factory;

    public function __construct(ResponseFactory $response_factory)
    {
        $this->response_factory = $response_factory;
    }

    /**
     * @param Connection $connection
     * @param Database $database
     * @param string $id
     * @throws \Exception
     * @return Response
     */
    public function getDocument(Connection $connection, $database, $id)
    {
        $value = $connection->request('GET', $database . '/' . $id);

        $response = $this->response_factory->make($value);

        if (!$response->success()) {
            throw new Exception();
        }

        return json_decode($response->body());
    }
}