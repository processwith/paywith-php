<?php
namespace ProcessWith\Processors\Paystack;

use Curl\Curl;
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
     * Optional fields of the transaction
     * 
     * @var array
     * @since 0.5
     */
    public $fields = [];

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
        return $reference;
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
    public function initialize( $fields = [] ) : void
    {
        if( array_key_exists('amount', $fields) ) {
            $this->amount = $fields['amount'];
        }

        if( array_key_exists('email', $fields) ) {
            $this->email = $fields['email'];
        }
        else {
            if( !$this->email ) {
                $this->email = sprintf('user%@gmail.com', time() );
            }
        }

        if( array_key_exists('fields', $fields) ) {
            $this->fields = $fields['fields'];
        }

        $this->body = [
            'amount'    => $this->amount,
            'email'     => $this->email,
            'meta_data' => $fields
        ];

        $curl = new Curl();
        $curl->setHeaders( $this->getHeaders() );
        $curl->post( sprintf('%s/initialize', $this->endpoint), $this->body);
        
        if( $curl->error ) {
            $this->statusCode       = $curl->errorCode;
            $this->statusMessage    = $curl->errorMessage;
        }
        else {
            $this->status       = true;
            $this->reference    = $curl->response->data->reference;
            $this->checkout_url = $curl->response->data->authorization_url;

            $this->setResponse($curl->response);
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
    public function verify(string $reference):bool
    {
        if(empty($reference)) {
            return false;
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