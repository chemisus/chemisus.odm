<?php

/**
 * @author Terrence Howard <chemisus@gmail.com>
 * @package chemisus.odm
 * @copyright 2011 Terrence Howard
 */

namespace Chemisus\ODM;

/**
 * @author Terrence Howard <chemisus@gmail.com>
 * @package chemisus.odm
 */
class Server
{
    /* \********************************************************************\ */
    /* \                            CONSTANTS                               \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            STATIC FIELDS                           \ */
    /* \********************************************************************\ */
    private static $Servers = array();

    /* \********************************************************************\ */
    /* \                            STATIC METHODS                          \ */
    /* \********************************************************************\ */
    /**
     *
     * @param string $location
     * @return Server
     */
    public static function Factory($address='localhost:5984')
    {
        if (!isset(self::$Servers[$address]))
        {
            self::$Servers[$address] = new Server($address);
        }
        
        return self::$Servers[$address];
    }

    /* \********************************************************************\ */
    /* \                            FIELDS                                  \ */
    /* \********************************************************************\ */
    /**
     *
     * @var Connection
     */
    private $connection;
    
    private $address;

    /* \********************************************************************\ */
    /* \                            PROPERTIES                              \ */
    /* \********************************************************************\ */
    public function getConnection()
    {
        return $this->connection;
    }
    
    public function getAddress()
    {
        return $this->address;
    }

    /* \********************************************************************\ */
    /* \                            CONSTRUCTORS                            \ */
    /* \********************************************************************\ */
    public function __construct($address)
    {
        $this->address = $address;
        
        $this->connection = Connection::Factory($address);
    }

    /* \********************************************************************\ */
    /* \                            PRIVATE METHODS                         \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            PROTECED METHODS                        \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            PUBLIC METHODS                          \ */
    /* \********************************************************************\ */
    public function createDatabase($name)
    {
        $transction = $this->tryCreateDatabase($name);
        
        return !$transaction['error'];
    }
    
    public function tryCreateDatabase($name)
    {
        return $this->connection->put($name);
    }

    public function deleteDatabase($name)
    {
        $transction = $this->tryDeleteDatabase($name);
        
        return !$transaction['error'];
    }
    
    public function tryDeleteDatabase($name)
    {
        return $this->connection->delete($name);
    }
    
    public function getDatabase($name)
    {
        return Database::Factory($this->address, $name);
    }
}