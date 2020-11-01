<?php

namespace ProcessWith\Paywith;

use Curl\Curl;
use ProcessWith\Paywith\Helpers\DoWork;

/**
 * The To class
 * 
 * @author To
 * @link https://www.processwith.com/paywith
 * @version 0.5
 */
class Paywith
{
    /**
     *  List of supported payment gateways
     * 
     * @version since 0.5
     */
    protected $gateways = [
        'paystack',
        'flutterwave',
        'monnify',
        'paylink'
    ];
    
    /**
     * The current gateway in use
     */
    protected $gateway;

    /**
     * API keys
     */
    private $secret_key;

    /**
     * The current gateway request
     * @since 0.5
     */
    public $request;

    /**
     * Store the request response
     */
    public $response = [
        'status'    => false,
        'code'      => 201,
        'message'   => "No response"
    ];

    /**
     * The status code and message of a http request
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
     * To response headers
     * 
     * @param stdClass
     */
    public $responseHeaders;

    /**
     * The error code and message from a gateway request response
     * 
     * @param int $errorCode
     * @param string $errorMessage
     */
    public $errorCode;
    public $errorMessage;

    /**
     * Every single gateway have their own response from a request
     * 
     * This variable temporary store a response from a gateway
     * 
     * @param stdClass
     */
    public $gatewayResponse;

    /**
     * The Payment Gateway API URL
     * 
     * e.g https://api.paystack.co
     */
    public $url;

    /**
     * The current endpoint we are making request to
     * 
     * e.g $url/transaction
     * 
     * @param string
     */
    protected $endpoint;
    

    /**
     * Constructor
     * 
     * Populate the gateway and other important properties
     * 
     * @param string $gateway is the name of the gateway
     * @param string $secret_key is the secret of a gateway
     */
    public function __construct($gateway, $secretKey) {
        // if the gateway is supported
        if (in_array( $gateway, $this->gateways)) {
            $this->gateway = $gateway;
            $this->secret_key = $secretKey;

            $this->setGatewayRequest();
        }
        else {
            die('Payment gateway not supported');
        }
    }

    /**
     * Set the secret key of a gateway
     * 
     * @param string $secretKey
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * Set the gateway urls, endpoint and headers
     * 
     * These values are fetch from the To database
     * 
     */
    private function setGatewayRequest()
    {
        $gateway = $this->gateway ?: '';

        $curl = new Curl();
        $curl->setHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => sprintf('Bearer %s', $this->secretKey),
        ]);
        $curl->get(
            sprintf('%s/headers', $this->$url),
            [
                'gateway' => $gateway,
            ]
        );
        
        if($curl->error) {
            // an error occured
            $this->statusCode       = $curl->errorCode;
            $this->statusMessage    = $curl->errorMessage;
        }
        else {
            // yes, did it
            $this->statusCode       = $curl->httpStatusCode;
            $this->statusMessage    = $curl->response->message;
            $this->requestHeaders   = $curl->response->data;
        }
    }

    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Request the Payment Gateway for a transaction
     * 
     * If the gateway matches a particular gateway, run a request for that gateway
     * 
     * @since 0.5 
     */
    public function transaction(): object
    {
        $transaction = null;

        switch($this->name) {
            case 'paystack':

            break;
            case 'flutterwave':
            break;
            default:
        }

        if(!$transaction) {
            return $transaction;
        }
        if ( $this->gateway === 'paystack' ) {
            $this->request['body'] = [
                'amount' => $data['amount'],
                'email' => $data['email']
            ];

            $curl = new Curl();
            $curl->setHeader('Content-Type', "application/json");
            $curl->setHeader('Authorization', sprintf("Bearer %s", $this->secret_key));
            $curl->post(
                sprintf('%s/transaction/initialize', $this->endpoint),
                $this->request['body']
            );

            $this->statusCode = $curl->httpStatusCode;

            if ($curl->error) {
                $this->response['message']  = $curl->errorMessage;
                $this->response['code']     = $curl->errorCode;
            }
            else {
                $this->response['body'] = $curl->getResponse();
                $this->response['code'] = $curl->httpStatusCode;
            }
        }
    }
}
