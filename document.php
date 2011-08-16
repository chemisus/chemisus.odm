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


    /* \********************************************************************\ */
    /* \                            STATIC FIELDS                           \ */
    /* \********************************************************************\ */
    protected static $Documents = array();

    /* \********************************************************************\ */
    /* \                            STATIC METHODS                          \ */
    /* \********************************************************************\ */
    public static function Factory($value)
    {
        if (is_object($value))
        {
            $value = get_class($value);
        }
        
        if (!isset(self::$Documents[$value]))
        {
            self::$Documents[$value] = new Document($value);
        }
        
        return self::$Documents[$value];
    }

    public static function Prototype($class)
    {
        $serialized = sprintf('O:%u:"%s":0:{}', strlen($class), $class);
        
        $unserialized = unserialize($serialized);
        
        return $unserialized;
    }
    
    public static function Convert($value)
    {
        if (is_object($value))
        {
            return self::ConvertObject($value);
        }
        else if (is_array($value))
        {
            return self::ConvertArray($value);
        }
        else if (is_null($value))
        {
            return $value;
        }
        else
        {
            return $value;
        }
    }
    
    protected static function ConvertObject($value)
    {
        return self::Factory($value)->toDocument($value);
    }
    
    protected static function ConverArray($value)
    {
    }

    /* \********************************************************************\ */
    /* \                            FIELDS                                  \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            PROPERTIES                              \ */
    /* \********************************************************************\ */
    protected $parents = array();
    
    protected $class;
    
    protected $fields = array();
    
    protected $methods = array();

    /* \********************************************************************\ */
    /* \                            CONSTRUCTORS                            \ */
    /* \********************************************************************\ */
    protected function __construct($class)
    {
        $parent = $class;
        
        while ($parent = get_parent_class($parent))
        {
            $this->parents[] = self::Factory($parent);
        }
        
        $this->class = new \ReflectionClass($class);

        $this->fields();
        
        $this->methods();
    }

    /* \********************************************************************\ */
    /* \                            PRIVATE METHODS                         \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            PROTECED METHODS                        \ */
    /* \********************************************************************\ */
    protected function fields()
    {
        $this->fields = array(
            'public' => $this->class->getProperties(\ReflectionProperty::IS_PUBLIC),
            'protected' => $this->class->getProperties(\ReflectionProperty::IS_PROTECTED),
            'private' => $this->class->getProperties(\ReflectionProperty::IS_PRIVATE),
        );

        foreach ($this->fields as &$fields)
        {
            foreach ($fields as $key=>$value)
            {
                $value->setAccessible(true);
                
                if ($value->getDeclaringClass()->getName() !== $this->class->getName())
                {
                    unset($fields[$key]);
                }
            }
        }
    }

    protected function methods()
    {
        $this->methods = array(
            'public' => $this->class->getMethods(\ReflectionMethod::IS_PUBLIC),
            'protected' => $this->class->getMethods(\ReflectionMethod::IS_PROTECTED),
            'private' => $this->class->getMethods(\ReflectionMethod::IS_PRIVATE),
        );
        
        foreach ($this->methods as &$methods)
        {
            foreach ($methods as $key=>$value)
            {
                $value->setAccessible(true);

                if (count($value->getParameters()) > 0 || $value->getDeclaringClass()->getName() !== $this->class->getName())
                {
                    unset($methods[$key]);
                }
            }
        }
    }

    /* \********************************************************************\ */
    /* \                            PUBLIC METHODS                          \ */
    /* \********************************************************************\ */
    public function toDocument($value)
    {
        $array = array(
            '#class' => $this->class->getName(),
            '#base' => array(),
        );
        
        foreach ($this->parents as $parent)
        {
            $array['#base'][$parent->class->getName()] = array();
            
            foreach ($parent->fields['private'] as $field)
            {
                $field->setAccessible(true);
                
                $array['#base'][$parent->class->getName()][$field->getName()] = self::Convert($field->getValue($value));
            }
        }
        
        foreach ($this->fields as $fields)
        {
            foreach ($fields as $field)
            {
                $array[$field->getName()] = self::Convert($field->getValue($value));
            }
        }
        
        foreach ($this->methods as $methods)
        {
            foreach ($methods as $method)
            {
                $array['@'.$method->getName()] = self::Convert($method->invoke($value));
            }
        }
        
        return $array;
    }
}