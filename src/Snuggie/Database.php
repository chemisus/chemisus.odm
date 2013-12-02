<?php

namespace Snuggie;

class Database
{
    private $server;
    private $name;

    public function __construct(Server $server, $name)
    {
        $this->server = $server;
        $this->name = $name;
    }

    public function createDocument($value)
    {
        return $this->server->connection()->post('/' . $this->name, json_encode($value));
    }
}