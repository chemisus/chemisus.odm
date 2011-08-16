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
class TestA
{
    /* \********************************************************************\ */
    /* \                            CONSTANTS                               \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            STATIC FIELDS                           \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            STATIC METHODS                          \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            FIELDS                                  \ */    
    /* \********************************************************************\ */
    /**
     * 
     * @field
     */
    private $_id;

    /**
     * 
     * @field
     */
    private $_rev;
    
    /**
     *
     * @field
     */
    private $aa = '------';
    
    /**
     *
     * @field
     */
    protected $ab = '------';
    
    /**
     *
     * @field
     */
    public $ac = '------';
    
    public $ad = '------';

    /* \********************************************************************\ */
    /* \                            PROPERTIES                              \ */
    /* \********************************************************************\ */
    public function setId($value)
    {
        $this->_id = $value;
    }
    
    public function setRev($value)
    {
        $this->_rev = $value;
    }
    
    /**
     *
     * @field
     */
    private function getAA()
    {
        return strtoupper($this->aa);
    }
    
    public function setAA($value)
    {
        $this->aa = $value;
    }

    /**
     *
     * @field
     */
    protected function getAB()
    {
        return strtoupper($this->ab);
    }
    
    public function setAB($value)
    {
        $this->ab = $value;
    }

    /**
     *
     * @field
     */
    public function getAC()
    {
        return strtoupper($this->ac);
    }
    
    public function setAC($value)
    {
        $this->ac = $value;
    }

    public function getAD()
    {
        return strtoupper($this->ad);
    }
    
    public function setAD($value)
    {
        $this->ad = $value;
    }

    /* \********************************************************************\ */
    /* \                            CONSTRUCTORS                            \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            PRIVATE METHODS                         \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            PROTECED METHODS                        \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            PUBLIC METHODS                          \ */
    /* \********************************************************************\ */
}