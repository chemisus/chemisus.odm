<?php

namespace Snuggie;

/**
 * Class IdGenerator provides a method of generating ids via requesting ids from a CouchDB server. These ids will be
 * unique and should have been used before.
 *
 * @package Snuggie
 */
class IdGenerator
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
     * Obtains an array of randomly generated uuids from the server.
     *
     * @param Connection $connection
     * @param int $count
     * @return mixed
     */
    public function generateIds(Connection $connection, $count = 1)
    {
        $value = $connection->request('GET', '_uuids?count=' . $count);

        $response = $this->response_factory->make($value);

        return json_decode($response->body())->uuids;
    }

    /**
     * Obtains one randomly generated uuid from the server.
     *
     * @param Connection $connection
     * @return mixed
     */
    public function generateId(Connection $connection)
    {
        $count = 1;

        $ids = $this->generateIds($connection, $count);

        return array_shift($ids);
    }
}