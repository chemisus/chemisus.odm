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
class Model
{
    /* \********************************************************************\ */
    /* \                            CONSTANTS                               \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            STATIC FIELDS                           \ */
    /* \********************************************************************\ */
    private static $Models = array();

    /* \********************************************************************\ */
    /* \                            STATIC METHODS                          \ */
    /* \********************************************************************\ */
    /**
     *
     * @param string $class
     * @return Model
     */
    public static function Factory($class)
    {
        if (is_object($class))
        {
            $class = get_class($class);
        }
        
        if (!isset(self::$Models[$class]))
        {
            self::$Models[$class] = new Model($class);
        }
        
        return self::$Models[$class];
    }

    /* \********************************************************************\ */
    /* \                            FIELDS                                  \ */
    /* \********************************************************************\ */
    /**
     * 
     * @var string
     */
    private $class;
    
    /**
     * 
     * @var string
     */
    private $language = 'javascript';

    /**
     * 
     * @var array
     */
    private $views = array();
    
    /* \********************************************************************\ */
    /* \                            PROPERTIES                              \ */
    /* \********************************************************************\ */
    /**
     * @field class
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
    
    /**
     * @field language
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }
    
    /**
     * @field _id
     * @return string
     */
    public function getId()
    {
        return '_design/'.$this->getClass();
    }
    
    /**
     * @field views
     * @var array
     */
    public function getViews()
    {
        $views = $this->views;
        
        foreach ($views as &$view)
        {
            foreach ($view as $key=>&$value)
            {
                $lines = explode("\n", $value);
                
                foreach ($lines as &$line)
                {
                    $line = trim($line);
                }
                
                $value = implode("", $lines);
            }
        }
        
        return $views;
    }

    /* \********************************************************************\ */
    /* \                            CONSTRUCTORS                            \ */
    /* \********************************************************************\ */
    public function __construct($class)
    {
        $this->class = $class;
        
        $base = \Chemisus\ODM\Document::BASE;
        
        $this->views['all'] = array(
            'map' => "
                function (doc) {
                    for (class in doc.{$base}) {
                        if (class == 'Car') {
                            emit(class, doc);
                            break;
                        }
                    }
                }",
        );

        $this->views['count'] = array(
            'map' => "
                function (doc) {
                    for (class in doc.{$base}) {
                        if (class == 'Car') {
                            emit(class, doc);
                            break;
                        }
                    }
                }",
            'reduce' => "
                function (keys, values) {
                    return values.length;
                }",
        );
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
    public function install()
    {
        Database::Current()->createDocument($this);
    }

    public function update()
    {
    }
    
    public function uninstall()
    {
    }
}