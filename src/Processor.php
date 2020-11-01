<?php

namespace ProcessWith\Paywith;

/**
 * The blueprint class of any payment processor that is supported
 * 
 * @package Paywith
 * @subpackage Processor
 * @author ProcessWith
 * @since 0.5
 */
class Processor
{
    /**
     * The name of the payment processor
     * 
     * @var string
     * @since 0.5
     */
    public $name;
    
    /**
     * URL of the payment processor
     * 
     * @var string
     * @since 0.5
     */
    private $URL;
    
    /**
     * Secret or Private key of the processor
     * 
     * @var string
     * @since 0.5
     */
    private $secretKey;
    
    /**
     * The current API version
     * 
     * @var string
     * @since 0.5
     */
    private $versions;

    /**
     * The endpoints of the processor
     * 
     * @var array
     * @since 0.5
     */
    private $endpoints;

    /**
     * The request header of the payment processor
     * 
     * @var array
     * @since 0.5
     */
    private $headers;

    /**
     * The response body per request sent
     * 
     * @var array
     * @since 0.5
     */
    private $response;

    /**
     * Constructor
     * 
     * @since 3.5.0
     * 
     */
    public function __constructor(string $name, string $secretKey, string $URL)
    {
        $this->name         = $name;
        $this->secretKey    = $secretKey;
        $this->URL          = $URL;
    }

    /**
     * Set the name of the processor
     * 
     * @since 0.5
     */
    public function setName(string $name):void
    {
        $this->name = $name;
    }

    /**
     * Set the secret or private key of the processor
     * 
     * @since 0.5
     */
    public function setSecretKey(string $secret):void
    {
        $this->secretKey = $secret;
    }

    /**
     * Set the API URL of the processor
     * 
     * @since 0.5
     */
    public function setURL(string $URL):void
    {
        $this->URL = $URL;
    }

    /**
     * Set the endpoints of a processor
     * 
     * @since 0.5
     */
    public function setEndpoints(array $endpoints):void
    {
        $this->endpoints = $endpoints;
    }

    /**
     * Set the request headers
     * 
     * @since 0.5
     */
    public function setHeaders(array $headers):void
    {
        $this->headers = $headers;
    }
    
    /**
     * Set the response body got from a request
     * 
     * @since 0.5
     */
    public function setResponse(array $response):void
    {
        $this->response = $response;
    }

    /**
     * Get the name of the processor
     * 
     * @since 0.5
     */
    public function getName():string
    {
        return $this->name;
    }

    /**
     * Get the URL of the processor
     * 
     * @since 0.5
     */
    public function getURL():string
    {
        return $this->URL;
    }

    /**
     * Get all the endpoints of the processor
     * 
     * @since 0.5
     */
    public function getEndpoints():array
    {
        return $this->endpoints;
    }

    /**
     * Get the request headers of the processor
     * 
     * @since 0.5
     */
    public function getHeaders():array
    {
        return $this->headers;
    }

    /**
     * Get the response body of a request from the processor
     * 
     * @since 0.5
     */
    public function getResponse():string
    {
        return $this->response;
    }
}