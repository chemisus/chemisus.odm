<?php

namespace Snuggie;

class CreateDocument
{
    /**
     * @var ResponseFactory
     */
    private $response_factory;

    /**
     * @param ResponseFactory $response_factory
     */
    public function __construct(ResponseFactory $response_factory)
    {
        $this->response_factory = $response_factory;
    }

    /**
     * Attempts to create a document in the database with the given id and body.
     *
     * @param Connection $connection
     * @param string $database
     * @param string $id
     * @param string $body
     * @return mixed
     * @throws DocumentCreationException
     */
    public function createDocument(Connection $connection, $database, $id, $body)
    {
        $value = $connection->request('PUT', $database . '/' . $id, $body);

        $response = $this->response_factory->make($value);

        if ($response->status() !== '201' && $response->status() !== '202') {
            throw new DocumentCreationException();
        }

        return json_decode($response->body());
    }
}
