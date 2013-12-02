<?php

namespace Snuggie;

class Server
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function createDatabase($name)
    {
        $this->connection->put('/' . $name);
    }

    public function hasDatabase($name)
    {
        $this->connection->get('/' . $name);
    }

    public function deleteDatabase($name)
    {
        $this->connection->delete('/' . $name);
    }
}