<?php

namespace Snuggie;

class Server
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function connection()
    {
        return $this->connection;
    }

    public function createDatabase($name)
    {
        return $this->connection->put('/' . $name);
    }

    public function hasDatabase($name)
    {
        return $this->connection->get('/' . $name) !== '{"error":"not_found","reason":"no_db_file"}' . "\n";
    }

    public function deleteDatabase($name)
    {
        return $this->connection->delete('/' . $name);
    }
}