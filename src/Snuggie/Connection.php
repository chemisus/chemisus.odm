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
class Connection
{
    /* \********************************************************************\ */
    /* \                            CONSTANTS                               \ */
    /* \********************************************************************\ */
    const ADDRESS = 'localhost';
    
    const PORT = 5984;

    /* \********************************************************************\ */
    /* \                            STATIC FIELDS                           \ */
    /* \********************************************************************\ */
    protected static $Connections = array();
    
    protected static $Status = array(
        200
    );

    /* \********************************************************************\ */
    /* \                            STATIC METHODS                          \ */
    /* \********************************************************************\ */
    public static function Factory($address=self::ADDRESS)
    {
        $url = "http://{$address}/";
        
        if (!isset(self::$Connections[$url]))
        {
            self::$Connections[$url] = new Connection($url);
        }
        
        return self::$Connections[$url];
    }
    
    public static function Response($response)
    {
        $response = array_combine(
            array('head', 'body'),
            explode("\r\n\r\n", $response, 2)
        );
        
        $head = explode("\r\n", $response['head']);
        
        $response['head'] = array();
        
        $status = $head[0];
        
        unset($head[0]);
        
        $status = explode(' ', $status, 3);
        
        $response['head']['protocol'] = $status[0];
        
        $response['head']['code'] = $status[1];
        
        $response['head']['message'] = $status[2];
        
        foreach ($head as $value)
        {
            $value = explode(':', $value, 2);
            
            $response['head'][strtolower(trim($value[0]))] = trim($value[1]);
        }
        
        $response['body'] = json_decode($response['body'], true);
        
        $response['error'] = false;
        
        return $response;
    }
    
    public static function CheckError(&$response)
    {
        if (isset($response['body']['error']))
        {
            $response['error'] = $response['body']['error'];
            
            $response['error'] = array(
                'error' => $response['body']['error'],
                'reason' => $response['body']['reason'],
            );
        }
    }
    
    public static function CheckStatus(&$response)
    {
        foreach (self::$Status as $status)
        {
            if ((integer)$response['head']['code'] === $status)
            {
                return;
            }
        }

        $response['error'] = array(
            'error' => $response['body']['error'],
            'reason' => $response['body']['reason'],
        );
    }

    /* \********************************************************************\ */
    /* \                            FIELDS                                  \ */
    /* \********************************************************************\ */
    protected $defaults = array();
    
    protected $url;

    /* \********************************************************************\ */
    /* \                            PROPERTIES                              \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            CONSTRUCTORS                            \ */
    /* \********************************************************************\ */
    protected function __construct($url)
    {
        $this->defaults[CURLOPT_URL] = $this->url = $url;
        
        $this->defaults[CURLOPT_RETURNTRANSFER] = true;
        
        $this->defaults[CURLOPT_HEADER] = true;
    }

    /* \********************************************************************\ */
    /* \                            PRIVATE METHODS                         \ */
    /* \********************************************************************\ */


    /* \********************************************************************\ */
    /* \                            PROTECED METHODS                        \ */
    /* \********************************************************************\ */
    protected function fetch($options)
    {
        $request = $this->defaults;
        
        foreach ($options as $key=>$value)
        {
            $request[$key] = $value;
        }
        
        $handler = curl_init();
        
        curl_setopt_array($handler, $request);
        
        $transaction = array(
            'request' => $request,
            'response' => self::Response(curl_exec($handler))
        );
        
        curl_close($handler);
        
        return $transaction;
    }

    /* \********************************************************************\ */
    /* \                            PUBLIC METHODS                          \ */
    /* \********************************************************************\ */
    public function get($url=null)
    {
        $transaction = $this->fetch(array(
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_URL => $this->url.trim($url, '/'),
        ));
        
        self::CheckStatus($transaction['response']);
        
        return $transaction;
    }
    
    public function head($url=null)
    {
        $transaction = $this->fetch(array(
            CURLOPT_CUSTOMREQUEST => 'HEAD',
            CURLOPT_URL => $this->url.trim($url, '/'),
        ));
        
        self::CheckStatus($transaction['response']);
        
        return $transaction;
    }
    
    public function put($url, $value)
    {
        $transaction = $this->fetch(array(
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_URL => $this->url.trim($url, '/'),
            CURLOPT_POSTFIELDS => json_encode($value),
        ));
        
        self::CheckError($transaction['response']);
        
        return $transaction;
    }
    
    public function post($url, $value)
    {
        $transaction = $this->fetch(array(
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_URL => $this->url.trim($url, '/'),
            CURLOPT_POSTFIELDS => json_encode($value, JSON_FORCE_OBJECT),
            CURLOPT_HTTPHEADER => array('Content-Type: application/json')
        ));
        
        self::CheckError($transaction['response']);
        
        return $transaction;
    }
    
    public function delete($url)
    {
        $transaction = $this->fetch(array(
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_URL => $this->url.trim($url, '/'),
        ));
        
        self::CheckError($transaction['response']);
        
        return $transaction;
    }
    
    public function copy()
    {
        
    }
}