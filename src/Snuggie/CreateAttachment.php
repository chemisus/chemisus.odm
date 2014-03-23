<?php

namespace Snuggie;

class CreateAttachment
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

    public function createAttachment($connection, $database, $id, $revision, $name, $file)
    {
        $value = $connection->request('PUT', $database . '/' . $id . '/' . $name . '?rev=' . $revision, $file);

        $response = $this->response_factory->make($value);

        if ($response->status() !== '201' && $response->status() !== '202') {
            throw new AttachmentCreationException();
        }

        return json_decode($response->body());
    }
}
