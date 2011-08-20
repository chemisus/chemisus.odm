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

    /* \********************************************************************\ */
    /* \                            STATIC METHODS                          \ */
    /* \********************************************************************\ */
    public static function Factory($server, $database)
    {
        if (!isset(self::$Databases[$server.'/'.$database]))
        {
            self::$Databases[$server.'/'.$database] = new Database($server, $database);
        }
        
        return self::$Databases[$server.'/'.$database];
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