<?php

namespace ProcessWith\Processors;

/**
 * The blueprint class of any payment processor that is supported
 * 
 * @package ProcessWith
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
     * Secret/Private key of the merchant
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
    protected $response;

    /**
     * The status code a processor request
     * 
     * @var bool $status
     */
    protected $status = false;

    /**
     * The status code and message of a http request
     * 
     * @var int
     */
    protected $statusCode;

    /**
     * The status code and message of an http request
     * 
     * @var string
     */
    protected $statusMessage;

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
     * Get the name of the processor 
     * 
     * @since 0.5
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the secret/private key of the merchant 
     * 
     * @since 0.5
     */
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    /**
     * Get the URL of the gateway 
     * 
     * @since 0.5
     */
    public function getURL(): string
    {
        return $this->URL;
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
    public function getResponse(): object
    {
        return (object) $this->response;
    }

    public function status() : bool
    {
        return $this->status;
    }

    public function statusMessage() : string
    {
        return $this->statusMessage;
    }

    public function statusCode() : int
    {
        return $this->statusCode;
    }
}