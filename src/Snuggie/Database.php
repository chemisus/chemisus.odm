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

    public function setValue(&$object, $key, $value)
    {
        if ($object instanceof \stdClass) {
            $object->{$key} = $value;
        } else {
            if (is_array($object)) {
                $object[$key] = $value;
            }
        }
    }

    public function getValue($object, $key)
    {
        if ($object instanceof \stdClass) {
            return $object->{$key};
        } else {
            if (is_array($object)) {
                return $object[$key];
            }
        }
    }

    public function createDocument($value)
    {
        return $this->server->connection()->post('/' . $this->name, json_encode($value), true);
    }

    public function insertDocument($id, $value)
    {
        return $this->server->connection()->put('/' . $this->name . '/' . $id, json_encode($value), true);
    }

    public function updateDocument($id, $revision, $value)
    {
        $this->setValue($value, '_id', $id);
        $this->setValue($value, '_rev', $revision);

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

    public function getDocument($id)
    {
        return json_decode(
            Response::Factory($this->server->connection()->get('/' . $this->name . '/' . $id, true))->body()
        );
    }

    public function insertView($id, $value)
    {
        return $this->server->connection()->put('/' . $this->name . '/_design/' . $id, json_encode($value), true);
    }

    public function hasView($id)
    {
        return $this->hasDocument('_design/' . $id);
    }

    public function runView($id, $method, $keys)
    {
        foreach ($keys as $key=>$value) {
            $keys[$key] = urlencode(json_encode($value));
        }

        return $this->getDocument('_design/' . $id . '/_view/' . $method . '?keys=%5B' . implode(',', $keys) . '%5D');
    }
}