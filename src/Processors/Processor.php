<?php declare(strict_types=1);

namespace PayWith\Processors;

use PayWith\Helpers\DoSomething;

/**
 * The blueprint class of any payment processor that is supported
 * 
 * @package PayWith
 * @subpackage Processor
 * @author PayWith
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
     * Curl request
     * 
     * @var Curl
     */
    protected $request;

    /**
     * The response body per request sent
     * 
     * @var array
     * @since 0.5
     */
    protected $response;

    /**
     * The raw response body per request sent
     * 
     * @var array
     * @since 0.5
     */
    protected $response_raw;

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
    protected $httpStatusCode;

    /**
     * The status code and message of an http request
     * 
     * @var string
     */
    protected $message;

    /**
     * Constructor
     * 
     * @since 3.5.0
     * 
     */
    public function __construct(string $name, string $secretKey, $URL)
    {
        $this->name         = ucwords($name);
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
        $this->name = ucwords($name);
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
        if(! DoSomething::goodURL($URL)) {
            throw new PayException($this->name .': API url not valid');
        }

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
     * Set the API URL of the processor
     * 
     * @since 0.5
     */
    public function setRawResponse($raw):void
    {
        $this->response_raw = $raw;
    }

    /**
     * Set the HTTP request message
     */
    final public function setMessage(string $message = '', int $code = 200): void
    {
        $this->name = \ucwords($this->name);

        if (200 == $code) {
            $code = $this->httpStatusCode ?? 0;
        }

        if (200 === $code) {
            $this->message = $this->name . ': ' . $message;
        }
        else if (201 === $code) {
            $this->message = $this->name . ': ' . $message;
        }
        else if (400 === $code) {
            $this->message = $this->name . ': Bad Request';
        }
        else if (401 === $code) {
            $this->message = $this->name . ': Unauthorised Request';
        }
        else if (404 === $code) {
            $this->message = $this->name . ': Not found';
        }
        else if (500 === $code) {
            $this->message = $this->name . ': Internal server error';
        }
        else if (501 === $code) {
            $this->message = $this->name . ': Service unavailable';
        }
    }

    /**
     * Get the name of the processor 
     * 
     * @since 0.5
     */
    public function getName(): string
    {
        return $this->name ?? '';
    }

    /**
     * Get the secret/private key of the merchant 
     * 
     * @since 0.5
     */
    public function getSecretKey(): string
    {
        return $this->secretKey ?? '';
    }

    /**
     * Get the URL of the gateway 
     * 
     * @since 0.5
     */
    public function getURL(): string
    {
        return $this->URL ?? '';
    }

    /**
     * Get the request headers of the processor
     * 
     * @since 0.5
     */
    public function getHeaders(): array
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

    /**
     * Get the raw response body of a request from the processor
     * 
     * @since 0.5
     */
    final public function getRawResponse()
    {
        return $this->response_raw;
    }

    final public function status(): bool
    {
        return $this->status;
    }

    final public function getMessage(): string
    {
        return $this->message ?? 'No request';
    }

    final public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode ?? 201;
    }
}