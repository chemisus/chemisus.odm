<?php

namespace Snuggie;

class Database
{
    private $server;
    private $name;

    public function __construct(Server $server, $name)
    {
        $this->server = $server;
        $this->name   = $name;
    }

    public function createDocument($value)
    {
        return $this->server->connection()->post('/' . $this->name, json_encode($value), true);
    }

    public function insertDocument($id, $value)
    {
        return $this->server->connection()->put('/' . $this->name . '/' . $id, json_encode($value), true);
    }

    public function hasDocument($id)
    {
        return Response::Factory(
            $this->server->connection()->head('/' . $this->name . '/' . $id, true)
        )->status() === '200';
    }

    public function deleteDocument($id, $revision)
    {
        return $this->server->connection()->delete('/' . $this->name . '/' . $id . '?rev=' . $revision, true);
    }
}