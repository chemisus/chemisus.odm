<?php

namespace Snuggie;

/**
 * Class GetDocument provides a method of requesting a document from a CouchDB database.
 *
 * @package Snuggie
 */
class GetDocument
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
     * Requests a document from the database.
     *
     * If the request is not successful, then getDocument will throw a DocumentNotFoundException.
     *
     * If the request is successful, then the body should contain a json object, which will be decoded, and then
     * returned.
     *
     * @param Connection $connection
     * @param Database $database
     * @param string $id
     * @throws DocumentNotFoundException
     * @return Response
     */
    public function getDocument(Connection $connection, $database, $id)
    {
        $value = $connection->request('GET', $database . '/' . $id);

        $response = $this->response_factory->make($value);

        if (!$response->success()) {
            throw new DocumentNotFoundException();
        }

        return json_decode($response->body());
    }
}