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
        $array = array();
        
        foreach ($value as $key=>$value)
        {
            $array[$key] = self::Convert($value);
        }
        
        return $array;
    }
    
    public static function Revert($value)
    {
        if (is_array($value) && isset($value['#class']))
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
        return self::Factory($data['#class'])->fromDocument($data);
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
        foreach ($this->properties as $property)
        {
            $doc = $property->getDocComment();

            $matches = array();
            
            if (!preg_match('/@field(?:\s(.*))?/', $doc, $matches))
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
            $doc = $property->getDocComment();

            $matches = array();
            
            if (!preg_match('/@field(?:\s(.*))?/', $doc, $matches))
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

            if ($value->getDeclaringClass()->getName() !== $this->class->getName())
            {
                unset($this->methods[$key]);
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
            '#object' => array(),
        );
        
        $array['#object'][$this->class->getName()] = array();
            
        foreach ($this->properties as $property)
        {
            $property->setAccessible(true);
            
            $array['#object'][$this->class->getName()][$property->getName()] = self::Convert($property->getValue($value));
        }
        
        foreach ($this->methods as $method)
        {
            $method->setAccessible(true);
            
            $array['#object'][$this->class->getName()]['@'.$method->getName()] = self::Convert($method->invoke($value));
        }
        
        foreach ($this->parents as $parent)
        {
            $array['#object'][$parent->class->getName()] = array();
            
            foreach ($parent->properties as $property)
            {
                $property->setAccessible(true);
                
                $array['#object'][$parent->class->getName()][$property->getName()] = self::Convert($property->getValue($value));
            }
            
            foreach ($parent->methods as $method)
            {
                $array['#object'][$parent->class->getName()]['@'.$method->getName()] = self::Convert($method->invoke($value));
            }
        }
        
        foreach ($this->fields as $key=>$field)
        {
            if ($key === '_rev' && $field->getValue($value) === null)
            {
                continue;
            }
            
            $array[$key] = $field->getValue($value);
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
            
                $property->setValue($object, self::Revert($data['#object'][$parent->class->getName()][$property->getName()]));
            }
        }
        
        foreach ($this->properties as $property)
        {
            $property->setAccessible(true);
            
            $property->setValue($object, self::Revert($data['#object'][$this->class->getName()][$property->getName()]));
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