<?php

namespace ProcessWith;

use Curl\Curl;
use ProcessWith\Helpers\DoSomething;

/**
 * The Processwith class
 * 
 * @author ProcessWith
 * @link https://www.processwith.com
 * @version 0.5
 */
class ProcessWith
{
    /**
     *  List of supported payment gateways
     * 
     * @var array
     * @version since 0.5
     */
    protected $processors = [
        'paystack',
        'flutterwave',
        'monnify',
        'paylink'
    ];
    
    /**
     * The current processor in use
     * 
     * @var string
     * @since 0.5
     */
    protected $processor;

    /**
     * The API secret key
     * 
     * @var string
     * @since 0.5
     */
    private $secretKey;

    /**
     * The current gateway request
     * 
     * @var array
     * @since 0.5
     */
    public $request;

    /**
     * Store the default response
     * 
     * @var array
     * @since 0.5
     */
    public $response = [
        'status'    => false,
        'message'   => '',
        'data'      => []
    ];

    /**
     * The status code and message of a http request
     * 
     * @param int $statusCode
     * @param string $statusMessage
     */
    public $statusCode;
    public $statusMessage;

    /**
     * Request headers
     * @param stdClass
     */
    public $requestHeaders;

    /**
     * The error code and message from a gateway request response
     * 
     * @param int $errorCode
     * @param string $errorMessage
     */
    public $errorCode;
    public $errorMessage;
    

    /**
     * Constructor
     * 
     * @since 0.5
     */
    public function __construct(string $processor, string $secretKey = '') {
        $this->processor = $processor;
        $this->secretKey = $secretKey;

        // if the Processor is not supported
        if( ! in_array($processor, $this->processors) ) {
            die('Processor not supported');
        }
    }

    /**
     * Set the secret key of a gateway
     * 
     * @param string $secretKey
     */
    public function setSecretKey(string $secretKey): void
    {
        $this->secretKey = $secretKey;
    }

    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * Request the Payment Processor for a transaction
     * 
     * If the processor matches a particular gateway, run a request for that gateway
     * 
     * @since 0.5 
     */
    public function transaction(): ?object
    {
        $transaction = null;

        switch(strtolower($this->processor)) {
            case 'paystack':
                $transaction = new Paystack\Transaction($this->secretKey);
            break;
            case 'flutterwave':
                $transaction = new Ravepay\Transaction($this->secretKey);
            break;
            default:
        }

        return $transaction;
    }
}
