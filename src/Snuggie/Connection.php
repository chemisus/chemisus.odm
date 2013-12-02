<?php

namespace Snuggie;

class Connection
{
    private $host;
    private $port;

    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function init($options)
    {
        $curl = curl_init();

        curl_setopt_array(
            $curl,
            $options
        );

        return $curl;
    }

    public function open($curl)
    {
        return curl_exec($curl);
    }

    public function close($curl)
    {
        curl_close($curl);
    }

    /**
     * @param $options
     * @return string
     */
    public function request($options)
    {
        $curl = $this->init($options);

        $response = $this->open($curl);

        $this->close($curl);

        return $response;
    }

    /**
     * @param $url
     * @param bool $return_headers
     * @return string
     */
    public function get($url, $return_headers = false)
    {
        return $this->request(
            [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HEADER         => $return_headers,
                CURLOPT_CUSTOMREQUEST  => 'GET',
                CURLOPT_PORT           => $this->port,
                CURLOPT_URL            => $this->host . $url,
            ]
        );
    }

    /**
     * @param $url
     * @param null $value
     * @param bool $return_headers
     * @param string $content_type
     * @return string
     */
    public function put($url, $value = null, $return_headers = false, $content_type = 'application/json')
    {
        return $this->request(
            [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HEADER         => $return_headers,
                CURLOPT_CUSTOMREQUEST  => 'PUT',
                CURLOPT_PORT           => $this->port,
                CURLOPT_URL            => $this->host . $url,
                CURLOPT_POSTFIELDS     => $value,
                CURLOPT_HTTPHEADER     => ['Content-Type: ' . $content_type]
            ]
        );
    }

    /**
     * @param $url
     * @param null $value
     * @param bool $return_headers
     * @param string $content_type
     * @return string
     */
    public function post($url, $value = null, $return_headers = false, $content_type = 'application/json')
    {
        return $this->request(
            [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HEADER         => $return_headers,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_PORT           => $this->port,
                CURLOPT_URL            => $this->host . $url,
                CURLOPT_POSTFIELDS     => $value,
                CURLOPT_HTTPHEADER     => ['Content-Type: ' . $content_type]
            ]
        );
    }

    /**
     * @param $url
     * @param bool $return_headers
     * @return string
     */
    public function delete($url, $return_headers = false)
    {
        return $this->request(
            [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HEADER         => $return_headers,
                CURLOPT_CUSTOMREQUEST  => 'DELETE',
                CURLOPT_PORT           => $this->port,
                CURLOPT_URL            => $this->host . $url,
            ]
        );
    }
}