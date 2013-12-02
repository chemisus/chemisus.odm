<?php

/**
 * @author Terrence Howard <chemisus@gmail.com>
 * @package snuggie
 * @copyright 2011 Terrence Howard
 */

namespace Snuggie;

/**
 * @author Terrence Howard <chemisus@gmail.com>
 * @package snuggie
 */
class View
    implements \Iterator
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
    private $total_rows = 0;
    
    private $offset = 0;
    
    private $rows = array();

    /* \********************************************************************\ */
    /* \                            PROPERTIES                              \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            CONSTRUCTORS                            \ */
    /* \********************************************************************\ */
    public function __construct($data)
    {
        print_r($data);
        
        if (isset($data['total_rows']))
        {
            $this->total_rows = $data['total_rows'];
        }
        
        if (isset($data['offset']))
        {
            $this->offset = $data['offset'];
        }
        
        foreach ($data['rows'] as $row)
        {
            if (isset($row['id']))
            {
                $this->rows[$row['id']] = array(
                    'key' => $row['key'],
                    'value' => $row['value'],
                );
            }
            else
            {
                $this->rows[] = array(
                    'key' => $row['key'],
                    'value' => $row['value'],
                );
            }
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
    function rewind()
    {
        return reset($this->rows);
    }
    
    function current()
    {
        return current($this->rows);
    }
    
    function key()
    {
        return key($this->rows);
    }
    
    function next()
    {
        return next($this->rows);
    }
    
    function valid()
    {
        return key($this->rows) !== null;
    }
}