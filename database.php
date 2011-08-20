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
class Database
{
    /* \********************************************************************\ */
    /* \                            CONSTANTS                               \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            STATIC FIELDS                           \ */
    /* \********************************************************************\ */
    private static $Databases = array();

    private static $Default;
    
    /* \********************************************************************\ */
    /* \                            STATIC METHODS                          \ */
    /* \********************************************************************\ */
    /**
     *
     * @param type $server
     * @param type $database
     * @param type $default
     * @return Database
     */
    public static function Factory($server, $database, $default=false)
    {
        if (!isset(self::$Databases[$server.'/'.$database]))
        {
            self::$Databases[$server.'/'.$database] = new Database($server, $database);
        }
        
        if ($default)
        {
            self::$Default = $server.'/'.$database;
        }
        
        return self::$Databases[$server.'/'.$database];
    }
    
    /**
     *
     * @return Database
     */
    public static function Current()
    {
        return self::$Databases[self::$Default];
    }

    /* \********************************************************************\ */
    /* \                            FIELDS                                  \ */
    /* \********************************************************************\ */
    private $server;
    
    private $database;

    /* \********************************************************************\ */
    /* \                            PROPERTIES                              \ */
    /* \********************************************************************\ */
    public function getServer()
    {
        return Server::Factory($this->server);
    }

    /* \********************************************************************\ */
    /* \                            CONSTRUCTORS                            \ */
    /* \********************************************************************\ */
    public function __construct($server, $database)
    {
        $this->server = $server;
        
        $this->database = $database;
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
    public function createDocument($value)
    {
        $transaction = $this->tryCreateDocument($value);
        
        return !$transaction['response']['error'];
    }
    
    public function tryCreateDocument($value)
    {
        return $this->getServer()->getConnection()->post($this->database, Document::Convert($value));
    }
    
    public function getDocument($id)
    {
        $id = urlencode($id);
        
        $transaction = $this->getServer()->getConnection()->get($this->database.'/'.$id);
        
        return Document::Revert($transaction['response']['body']);
    }

    public function getView($view, $function)
    {
        $transaction = $this->getServer()->getConnection()->get($this->database.'/_design/'.$view.'/_view/'.$function);
        
        return new View(Document::Revert($transaction['response']['body']));
    }

    public function updateDocument($value)
    {
        $transaction = $this->tryUpdateDocument($value);
        
        return !$transaction['response']['error'];
    }
    
    public function tryUpdateDocument($value)
    {
        return $this->getServer()->getConnection()->post($this->database, Document::Convert($value));
    }
    
    public function deleteDocument($value)
    {
        $transaction = $this->tryDeleteDocument($value);
        
        return !$transaction['response']['error'];
    }
    
    public function tryDeleteDocument($value)
    {
        $array = Document::Convert($value);
        
        $id = $array['_id'];
        
        $rev = $array['_rev'];
        
        $transaction = $this->getServer()->getConnection()->delete($this->database.'/'.$id.'?rev='.$rev);

        return Document::Revert($transaction['response']['body']);
    }
}