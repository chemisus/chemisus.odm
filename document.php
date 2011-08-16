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
class Document
{
    /* \********************************************************************\ */
    /* \                            CONSTANTS                               \ */
    /* \********************************************************************\ */
    const FIELD = '@field';

    const PROPERTY = '@property';
    
    const SEPERATOR_OLD = '\\';
    
    const SEPERATOR_NEW = '\\';
    
    const BASE = '#base';
    
    const TYPE = '#class';

    /* \********************************************************************\ */
    /* \                            STATIC FIELDS                           \ */
    /* \********************************************************************\ */
    protected static $Documents = array();

    /* \********************************************************************\ */
    /* \                            STATIC METHODS                          \ */
    /* \********************************************************************\ */
    public static function Factory($document)
    {
        if (is_object($document))
        {
            $document = get_class($document);
        }
        
        if (!isset(self::$Documents[$document]))
        {
            self::$Documents[$document] = new Document($document);
        }
        
        return self::$Documents[$document];
    }

    public static function Prototype($class)
    {
        $serialized = sprintf('O:%u:"%s":0:{}', strlen($class), $class);
        
        $unserialized = unserialize($serialized);
        
        return $unserialized;
    }

    /* \********************************************************************\ */
    /* \                            FIELDS                                  \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            PROPERTIES                              \ */
    /* \********************************************************************\ */
    protected $class;
    
    protected $fields;
    
    protected $methods;

    /* \********************************************************************\ */
    /* \                            CONSTRUCTORS                            \ */
    /* \********************************************************************\ */
    protected function __construct($model)
    {
        $this->class = new \ReflectionClass($model);
        
        foreach ($this->class->getProperties() as $value)
        {
            $value->setAccessible(true);

            $doc = $value->getDocComment();

            $matches = array();
            
            if (!preg_match('/'.self::FIELD.'(?:\ (.*)?)?/', $doc, $matches))
            {
                continue;
            }

            $name = $value->getName();
            
            if (isset($matches[1]))
            {
                $name = $matches[1];
            }
            
            $this->fields[$name] = $value;
        }
        
        foreach ($this->class->getMethods() as $value)
        {
            $value->setAccessible(true);
            
            $doc = $value->getDocComment();
            
            if (!preg_match('/'.self::PROPERTY.'(?:\ (.*)?)?/', $doc, $matches))
            {
                continue;
            }

            $name = $value->getName();
            
            if (isset($matches[1]))
            {
                $name = $matches[1];
            }
            
            $this->methods[$name] = $value;
        }
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
    public function prepare($object)
    {
        $array = array();

        $array[self::TYPE] = str_replace(
            self::SEPERATOR_OLD,
            self::SEPERATOR_NEW,
            $this->class->getName()
        );

        if ($this->class->getParentClass())
        {
            $parent = self::Factory($this->class->getParentClass()->getName());
            
            foreach ($parent->prepare($object) as $key=>$value)
            {
                $array[$key] = $value;
            }
        }
        
        foreach ($this->fields as $key=>$value)
        {
            if ($key === '_rev' && !$value->getValue($object))
            {
                continue;
            }
            
            $array[$key] = $value->getValue($object);
        }
        
        foreach ($this->methods as $key=>$value)
        {
            $array[$key] = $value->invoke($object);
        }
        
        return $array;
    }
    
    public function initialize($array, $object=null)
    {
        if ($object === null)
        {
            $object = self::Prototype(str_replace(
                self::SEPERATOR_NEW,
                self::SEPERATOR_OLD,
                $array[self::TYPE]
            ));
        }

        if ($this->class->getParentClass())
        {
            $parent = self::Factory($this->class->getParentClass()->getName());
            
            $parent->initialize($array, $object);
        }
        
        foreach ($this->fields as $key=>$value)
        {
            $value->setValue($object, $array[$key]);
        }
        
        return $object;
    }
}