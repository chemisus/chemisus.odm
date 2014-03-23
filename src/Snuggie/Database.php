<?php

namespace Snuggie;

class Database
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Uploader
     */
    private $uploader;

    /**
     * @var string
     */
    private $database;

    /**
     * @var ResponseFactory
     */
    private $response_factory;

    /**
     * @var GetDocument
     */
    private $get_document;

    /**
     * @var CreateDocument
     */
    private $create_document;

    /**
     * @var CreateAttachment
     */
    private $create_attachment;

    /**
     * @var IdGenerator
     */
    private $id_generator;

    /**
     * @param Connection $connection
     * @param Uploader $uploader
     * @param string $database
     * @param [] $options
     */
    public function __construct(Connection $connection, Uploader $uploader, $database, $options = [])
    {
        $this->connection = $connection;
        $this->uploader = $uploader;
        $this->database = $database;

        $this->response_factory = $this->option($options, 'response_factory', new ResponseFactory());
        $this->get_document = $this->option($options, 'get_document', new GetDocument($this->response_factory));
        $this->create_document = $this->option($options, 'create_document', new CreateDocument($this->response_factory));
        $this->create_attachment = $this->option($options, 'create_attachment', new CreateAttachment($this->response_factory));
        $this->id_generator = $this->option($options, 'id_generator', new IdGenerator($this->response_factory));
    }

    public function option($options, $key, $otherwise)
    {
        return isset($options[$key]) ? $options[$key] : $otherwise;
    }

    public function getDocument($id)
    {
        return $this->get_document->getDocument($this->connection, $this->database, $id);
    }

    public function createDocument($id, $body)
    {
        return $this->create_document->createDocument($this->connection, $this->database, $id, $body);
    }

    public function createAttachment($id, $revision, $name, $file)
    {
        return $this->create_attachment->createAttachment($this->uploader, $this->database, $id, $revision, $name, $file);
    }

    public function generateId()
    {
        return $this->id_generator->generateId($this->connection);
    }

    public function generateIds($count = 1)
    {
        return $this->id_generator->generateIds($this->connection, $count);
    }
}