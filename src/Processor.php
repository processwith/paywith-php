<?php

namespace ProcessWith;

/**
 * The blueprint class of any payment processor that is supported
 * 
 * @package Paywith
 * @subpackage Processor
 * @author ProcessWith
 * @since 0.5
 */
abstract class Processor
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
    public $URL;
    
    /**
     * Secret or Private key of the processor
     * 
     * @var string
     * @since 0.5
     */
    private $secretKey;

    /**
     * The request header of the payment processor
     * 
     * @var array
     * @since 0.5
     */
    protected $headers;

    /**
     * The response body per request sent
     * 
     * @var array
     * @since 0.5
     */
    protected $response = [
        'status'    => false,
        'message'   => '',
        'client'    => [], 
    ];

    /**
     * The response body per request sent
     * 
     * @var array
     * @since 0.5
     */
    protected $client_response;

    /**
     * The status code a processor request
     * 
     * @var bool $status
     */
    public $status = false;

    /**
     * The status code and message of a http request
     * 
     * @var int
     */
    public $statusCode;

    /**
     * The status code and message of an http request
     * 
     * @var string
     */
    public $statusMessage;

    /**
     * Constructor
     * 
     * @since 3.5.0
     * 
     */
    public function __construct(string $name, string $secretKey, $URL)
    {
        $this->name         = $name;
        $this->secretKey    = $secretKey;
        $this->URL          = $URL;
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
     * Set the request headers
     * 
     * @since 0.5
     */
    abstract public function setHeaders(array $headers):void;

    /**
     * Set the response
     * 
     * @since 0.5
     */
    abstract public function setResponse(object $response):void;

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
    public function getResponse(): object
    {
        return (object) $this->response;
    }
}