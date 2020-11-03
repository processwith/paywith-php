<?php
namespace ProcessWith\Processors\Paystack;

use ProcessWith\Processors\Paystack\Paystack;

class Transaction extends Paystack
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
     * Metadata of the transaction
     * 
     * @var array
     * @since 0.5
     */
    public $metadata = [];

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
    public $callback_url;

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
        $this->endpoint = sprintf('%s/%s', $this->URL, $this->endpoints['transactions'] );
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
     * Initialize a Paystack transaction
     * 
     * ---------------------------------------------------------------------
     * We make a request to the /transaction endpoint
     * a response will be returned:
     * {
     *      ...
     *      "authorization_url": "https://checkout.paystack.com/0peioxfhpn",
     *      "access_code": "0peioxfhpn",
     *      "reference": "7PVGX8MEk85tgeEpVDtD"
     * }
     * 
     * We then set this response body
     * 
     * @param $redirect if it set to true, we redirect to the PSTK checkout page
     * @link https://paystack.com/docs/api/#transaction
     * @since 0.5
     */
    public function initialize( $body = [] ) : void
    {
        if( array_key_exists('amount', $body) ) {
            $this->amount = $body['amount'];
        }

        if( array_key_exists('email', $body) ) {
            $this->email = $body['email'];
        }
        else {
            if( !$this->email ) {
                $this->email = sprintf('user%@gmail.com', time() );
            }
        }

        if ( array_key_exists('callback_url', $body) ) {
            $this->callback_url = $body['callback_url'];
        }

        if ( array_key_exists('metadata', $body) ) {
            $this->callback_url = $body['metadata'];
        }

        $this->body = $body;

        $request = $this->request;
        $request->post( sprintf('%s/initialize', $this->endpoint), $this->body);
        
        if( $request->error ) {
            $this->statusCode       = $request->errorCode;
            $this->statusMessage    = $request->errorMessage;
        }
        else {
            $this->status       = true;
            $this->reference    = $request->response->data->reference;
            $this->checkout_url = $request->response->data->authorization_url;

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
     * @link https://paystack.com/docs/api/#transaction-verify
     * @since 0.5
     */
    public function verify(string $reference = ''): void
    {
        $this->status = false; // default status

        if(empty($reference)) {
            $reference = $this->reference;
        }

        if(empty($reference)) {
            $this->statusCode       = 400;
            $this->statusMessage    = 'No reference supplied';
        }

        $request = $this->request;
        $request->get(sprintf('%s/verify/%s', $this->endpoint, $reference));

        if($request->error) {
            $this->statusCode       = $request->errorCode;
            $this->statusMessage    = $request->errorMessage;
        }
        else {
            $this->setResponse($request->response);

            $this->reference    = $request->response->data->reference;
            $this->amount       = $request->response->data->amount;
            $this->email        = $request->response->data->customer->email;

            $this->statusMessage= $request->response->data->gateway_response;
            $this->statusCode   = $request->getHttpStatusCode();

            if('success' == $request->response->data->status) {
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
    public function webhook(): void
    {
        $this->status = false; // default status

        // only a post with paystack signature header gets our attention
        if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' ) || !array_key_exists('x-paystack-signature', $_SERVER) ) {
            $this->statusMessage    = 'Invalid Paystack POST signature';
            $this->statusCode       = 400;
        }

        // Retrieve the request's body
        $input = @file_get_contents("php://input");

        // validate event do all at once to avoid timing attack
        if ($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $input, $this->getSecretKey()))
        {
            $this->statusMessage    = 'Invalid Paystack event signature';
            $this->statusCode       = 400;
        }

        http_response_code(200);

        // parse event (which is json string) as object
        // Do something - that will not take long - with $event
        $event = json_decode($input);

        $this->amount   = $event->data->amount;
        $this->email    = $event->data->customer->email;
        $this->reference= $event->data->reference;

        $this->response         = $event;
        $this->status           = true;
        $this->statusCode       = 200;
        $this->statusMessage    = $event->data->gateway_response;
    }
}