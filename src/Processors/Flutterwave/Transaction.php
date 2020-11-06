<?php
namespace ProcessWith\Processors\Flutterwave;

use Curl\Curl;
use ProcessWith\Processors\Flutterwave\Flutterwave;
use ProcessWith\Exceptions\PayException;

class Transaction extends Flutterwave
{
    /**
     * The `amount` of the transaction
     * 
     * @var float
     * @since 0.5
     */
    public $amount = 0;

    /**
     * The `email` of the transaction
     * 
     * @var string
     * @since 0.5
     */
    public $email;

    /**
     * The customer 
     * 
     * @var array
     * @since 0.5
     */
    public $customer = [];

    /**
     * Transaction dafualt currency 
     * 
     * @var array
     * @since 0.5
     */
    public $currency;

    /**
     * fallback if payment option not set
     * 
     * @var array
     * @since 0.5
     */
    public $payOption = 'card';

    /**
     * Transaction meta data
     * 
     * @var array
     * @since 0.5
     */
    public $metaData = [];

    /**
     * The `reference` of the transaction
     * 
     * @var string
     * @since 0.5
     */
    protected $reference;

    /**
     * The request body of a transaction
     * 
     * @var array
     * @since 0.5
     */
    public $body;

    /**
     * The transacion endpoint
     * 
     * @var string
     * @since 0.5
     */
    public $endpoint;

    /**
     * The callback url
     * 
     * @var string
     */
    public $redirect_url;

    /**
     * Checkout url
     * 
     * @var string
     * @since 0.5
     */
    public $checkout_url;


    /**
     * Constructor
     * 
     * @since 0.5
     */
    public function __construct(string $secretKey)
    {
        parent::__construct($secretKey);
        $this->endpoint = sprintf('%s/%s', $this->URL, $this->endpoints['payments'] );
    }

    /**
     * Set the reference of the transaction
     * 
     * @since 0.5
     */
    public function setReference(string $reference):void
    {
        $this->reference = $reference;
    }

    /**
     * Get the reference of the transaction
     * 
     * @since 0.5
     */
    public function getReference():string
    {
        return $this->reference;
    }

    /**
     * Initialize a flutterwave transaction
     * 
     * ---------------------------------------------------------------------
     * We make a request to the /transaction endpoint
     * a response will be returned:
     * {
     *    "status":"success",
     *    "message":"Hosted Link",
     *    "data":{
     *      "link":"https://api.flutterwave.com/v3/hosted/pay/f524c1196ffda5556341"
     *    }
     * }
     * 
     * We then set this response body
     * 
     * @param $redirect if it set to true, we redirect to the Ravepay checkout page
     * @since 0.5
     */
    public function initialize(array $fields = []) : void
    {

        $this->currency = array_key_exists('currency', $fields) ? $fields['currency'] : 'NGN';

        $this->metaData = array_key_exists('meta', $fields) ? $fields['meta'] : $fields['customer'];

        if( array_key_exists('amount', $fields) ) {
            $this->amount = $fields['amount'];
        } 
        else {
            throw new PayException('amount field is required') ;
        }


        if( array_key_exists('redirect_url', $fields) ) {
            $this->redirect_url = $fields['redirect_url'];
        }
        else {
            throw new PayException('redirect_url field is required'); 
        }


        if( array_key_exists('customer', $fields) ) {
            if( array_key_exists('email',  $fields['customer']) ) {
                $this->customer = $fields['customer'];
            }
            else {
                throw new PayException("The consumer array field requires an email"); 
            }
        }
        else {
            throw new PayException("The consumer field is required");
        }
        

        $this->body = [
            'tx_ref'          => bin2hex(random_bytes(7)),
            'amount'          => $this->amount,
            'currency'        => $this->currency,
            'redirect_url'    => $this->redirect_url,
            'payment_options' => $this->payOption, 
            'meta'            => $this->metaData,
            'customer'        => $this->customer            
        ];

        $request = $this->request;
        $request->post( sprintf('%s/payments', $this->endpoint), $this->body);

        if( $request->error ) {
            $this->statusCode       = $request->errorCode;
            $this->statusMessage    = $request->errorMessage;
        }
        else {
            $this->status       = true;
            $this->reference    = $this->body['tx_ref'];
            $this->checkout_url = $request->response->data->link;

            $this->setResponse($request->response);
        }
    }

    public function checkout(): void
    {
        header( sprintf('Location:%s', $this->checkout_url) );
        die();
    }

    /**
     * Verify a transaction
     * 
     * -----------------------------------------------------------
     * By requesting to the /transaction/verify endpoint
     * a reponse body containing the transaction information is
     * returned.  
     * 
     * If the transaction status was successfull, we return TRUE
     * -----------------------------------------------------------
     * 
     * @link https://developer.flutterwave.com/docs/transaction-verification
     * @link https://api.flutterwave.com/v3/transactions/123456/verify
     * @since 0.5
     */
    public function verify(string $reference) : void
    {
        
        $this->status = false; // default status

        if( empty($reference) ) {
            $this->statusCode       = 400;
            $this->statusMessage    = 'No reference supplied';
        }

        $request = $this->request;
        $request->get(sprintf('%s/transactions/%s/verify', $this->endpoint, $reference));

        if($request->error) {
            $this->statusCode       = $request->errorCode;
            $this->statusMessage    = $request->errorMessage;
        }
        else {
            $this->setResponse($request->response);

            $this->reference       = $request->response->data->tx_ref;
            $this->amount          = $request->response->data->amount;
            $this->email           = $request->response->data->customer->email;
            $this->verifyResponse  = $request->response;

            $this->statusMessage= $request->response->data->processor_response;
            $this->statusCode   = $request->getHttpStatusCode();

            if('success' == $request->response->status) {
                $this->status = true;
            }
        }

    }

    /**
     * Handle webhook
     * 
     * When a transaction is made on Paystack, paystack sends a payload of
     * data to URL you specify on your dashboard.
     * 
     * This method handle the payload and return TRUE|FALSE for a 
     * valid or non valid transaction.
     * 
     * @link 
     * @since 0.5
     */
    public function webhook():bool
    {

    }
}