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
    const OBJECT = '$$object';

    const BASE = '$$base';
    
    const METHOD = '$';
    
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
        $array = array();
        
        foreach ($value as $key=>$value)
        {
            $array[$key] = self::Convert($value);
        }
        
        return $array;
    }
    
    public static function Revert($value)
    {
        if (is_array($value) && isset($value[self::OBJECT]))
        {
            return self::RevertObject($value);
        }
        else if (is_array($value))
        {
            return self::RevertArray($value);
        }
        else
        {
            return $value;
        }
    }
    
    public static function RevertObject($data)
    {
        return self::Factory($data[self::OBJECT])->fromDocument($data);
    }
    
    public static function RevertArray($data)
    {
        $array = array();
        
        foreach ($data as $key=>$value)
        {
            $array[$key] = self::Revert($value);
        }
        
        return $array;
    }

    /* \********************************************************************\ */
    /* \                            FIELDS                                  \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            PROPERTIES                              \ */
    /* \********************************************************************\ */
    protected $parents = array();
    
    protected $class;
    
    protected $properties = array();
    
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

        $this->properties();
        
        $this->methods();
        
        $this->fields();
    }

    /* \********************************************************************\ */
    /* \                            PRIVATE METHODS                         \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            PROTECED METHODS                        \ */
    /* \********************************************************************\ */
    protected function fields()
    {
        foreach (array_reverse($this->parents) as $parent)
        {
            foreach ($parent->properties as $property)
            {
                $doc = $property->getDocComment();

                $matches = array();

                if (!preg_match('/@field(?:\ (.*))?/', $doc, $matches))
                {
                    continue;
                }

                $name = $property->getName();

                if (count($matches) > 1)
                {
                    $name = $matches[1];
                }

                $this->fields[$name] = $property;
            }
        }

        foreach (array_reverse($this->parents) as $parent)
        {
            foreach ($parent->methods as $method)
            {
                if ($method->getName() === '__construct')
                {
                    continue;
                }
                else if ($method->getName() === '__destruct')
                {
                    continue;
                }
                else if (count($method->getParameters()) > 0)
                {
                    continue;
                }
                
                $doc = $method->getDocComment();

                $matches = array();

                if (!preg_match('/@field(?:\ (.*))?/', $doc, $matches))
                {
                    continue;
                }
                
                $name = $method->getName();

                if (count($matches) > 1)
                {
                    $name = $matches[1];
                }

                $this->fields[$name] = $method;
            }
        }
        
        foreach ($this->properties as $property)
        {
            $doc = $property->getDocComment();

            $matches = array();
            
            if (!preg_match('/@field(?:\ (.*))?/', $doc, $matches))
            {
                continue;
            }

            $name = $property->getName();
            
            if (count($matches) > 1)
            {
                $name = $matches[1];
            }
            
            $this->fields[$name] = $property;
        }
        
        foreach ($this->methods as $method)
        {
            if ($method->getName() === '__construct')
            {
                continue;
            }
            else if ($method->getName() === '__destruct')
            {
                continue;
            }
            else if (count($method->getParameters()) > 0)
            {
                continue;
            }

            $doc = $method->getDocComment();

            $matches = array();
            
            if (!preg_match('/@field(?:\ (.*))?/', $doc, $matches))
            {
                continue;
            }

            $name = $method->getName();
            
            if (count($matches) > 1)
            {
                $name = $matches[1];
            }
            
            $this->fields[$name] = $method;
        }
    }
    
    protected function properties()
    {
        $this->properties = $this->class->getProperties();

        foreach ($this->properties as $key=>$value)
        {
            $value->setAccessible(true);
            
            if ($value->getDeclaringClass()->getName() !== $this->class->getName())
            {
                unset($this->properties[$key]);
            }
        }
    }

    protected function methods()
    {
        $this->methods = $this->class->getMethods();
        
        foreach ($this->methods as $key=>$value)
        {
            $value->setAccessible(true);

            if ($value->getName() === '__construct')
            {
                unset($this->methods[$key]);
            }
            else if ($value->getName() === '__destruct')
            {
                unset($this->methods[$key]);
            }
            else if (count($value->getParameters()) > 0)
            {
                unset($this->methods[$key]);
            }
            else if ($value->getDeclaringClass()->getName() !== $this->class->getName())
            {
                unset($this->methods[$key]);
            }
        }
    }

    /* \********************************************************************\ */
    /* \                            PUBLIC METHODS                          \ */
    /* \********************************************************************\ */
    /**
     *
     * @param object $value
     * @return array
     */
    public function toDocument($value)
    {
        /**
         * every object must have at least #class and #object. the #class field
         * stores the name of the class that will be used when we are trying to
         * revert the object. the #object field is an array that includes the
         * class and inherited classes. each item in #object will also contain
         * the values for the fields defined ONLY in that class..
         */
        $array = array(
            self::OBJECT => $this->class->getName(),
            self::BASE => array(
                $this->class->getName() => array(),
            ),
        );

        /**
         * save each property declared in the current class.
         */
        foreach ($this->properties as $property)
        {
            $property->setAccessible(true);
            
            $array[self::BASE][$this->class->getName()][$property->getName()] = self::Convert($property->getValue($value));
        }
        
        /**
         * save the return value of each method declared in the current class.
         */
        foreach ($this->methods as $method)
        {
            $method->setAccessible(true);
            
            $array[self::BASE][$this->class->getName()][self::METHOD.$method->getName()] = self::Convert($method->invoke($value));
        }

        /**
         * for each parent class of the current class.
         */
        foreach ($this->parents as $parent)
        {
            /**
             * assign a spot in the array for the parent.
             */
            $array[self::BASE][$parent->class->getName()] = array();
            
            /**
             * save each property declared in the parent class.
             */
            foreach ($parent->properties as $property)
            {
                $property->setAccessible(true);
                
                $array[self::BASE][$parent->class->getName()][$property->getName()] = self::Convert($property->getValue($value));
            }
            
            /**
             * save the return value of each method declared in the parent class.
             */
            foreach ($parent->methods as $method)
            {
                $array[self::BASE][$parent->class->getName()][self::METHOD.$method->getName()] = self::Convert($method->invoke($value));
            }
        }

        /**
         * for each field, we will save the field value in the top level of the
         * document.
         */
        foreach ($this->fields as $key=>$field)
        {
            /**
             * if the field name is _rev, and its null, then we dont want to
             * save it.
             */
            if ($key === '_id' || $key === '_rev')
            {
                if ($field instanceof \ReflectionMethod && $field->invoke($value) === null)
                {
                    continue;
                }
                else if ($field instanceof \ReflectionProperty && $field->getValue($value) === null)
                {
                    continue;
                }
            }
            
            if ($field instanceof \ReflectionMethod)
            {
                $array[$key] = $field->invoke($value);
            }
            else if ($field instanceof \ReflectionProperty)
            {
                $array[$key] = $field->getValue($value);
            }
        }
        
        return $array;
    }
    
    public function fromDocument($data)
    {
        $object = self::Prototype($this->class->getName());
        
        foreach ($this->parents as $parent)
        {
            foreach ($parent->properties as $property)
            {
                $property->setAccessible(true);
            
                $property->setValue($object, self::Revert($data[self::BASE][$parent->class->getName()][$property->getName()]));
            }
        }
        
        foreach ($this->properties as $property)
        {
            $property->setAccessible(true);
            
            $property->setValue($object, self::Revert($data[self::BASE][$this->class->getName()][$property->getName()]));
        }
        
        foreach ($this->fields as $key=>$field)
        {
            if (!$field instanceof \ReflectionProperty)
            {
                continue;
            }
            
            $field->setAccessible(true);
            
            $field->setValue($object, self::Revert($data[$key]));
        }
            
        return $object;
    }
}