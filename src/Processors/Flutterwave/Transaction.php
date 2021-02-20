<?php declare(strict_types=1);

namespace PayWith\Processors\Flutterwave;

use PayWith\Helpers\DoSomething;
use PayWith\Exceptions\PayException;
use PayWith\Processors\Flutterwave\Flutterwave;

class Transaction extends Flutterwave
{
    /**
     * The `amount` of the transaction
     * 
     * @var float
     * @since 0.5
     */
    protected $amount = 1;

    /**
     * The request body of a transaction
     * 
     * @var array
     * @since 0.5
     */
    protected $body;

    /**
     * The transacion endpoint
     * 
     * @var string
     * @since 0.5
     */
    protected $endpoint;

    /**
     * Transaction dafault currency 
     * 
     * @var array
     * @since 0.5
     */
    protected $currency = 'NGN';

    /**
     * Checkout url
     * 
     * @var string
     * @since 0.5
     */
    protected $checkout_url;

    /**
     * Fallback if payment option not set
     * 
     * @var string
     * @since 0.5
     */
    protected $payment_option = 'card, ussd, banktransfer';

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
     * Set a transaction request endpoint
     * 
     * @since 0.5
     */
    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Set the transaction request body
     * 
     * @since 0.5
     */
    public function setBody(array $body): void
    {
        $this->body = $body;
    }

    /**
     * Get the endpoint of the transaction
     * 
     * @since 0.5
     */
    public function getEndpoint(): string
    {
        return ($this->endpoint ?? '');
    }

    /**
     * Get the body of the transaction
     * 
     * @since 0.5
     */
    public function getBody(): object
    {
        return (object) $this->body;
    }

    /**
     * Initialize a Flutterwave transaction
     * 
     * ---------------------------------------------------------------------
     * We make a request to the /transaction endpoint
     * a response will be returned:
     * {
     *      ...
     
     * }
     * 
     * We then set this response body
     * 
     * @param $redirect if it set to true, we redirect to the PSTK checkout page
     * @link https://developer.flutterwave.com/docs/flutterwave-standard
     * @since 0.5
     */
    public function initialize(array $body = []) //: void
    {
        if( ! array_key_exists('amount', $body) ) {
            throw new PayException('Transaction: Amount is required on Flutterwave');
        }

        if ( ! isset($body['customer']['email']) ) {
            throw new PayException('Transaction: Customer email is required on Flutterwave');
        }

        if( ! array_key_exists('tx_ref', $body) ) {
            $body['tx_ref'] = bin2hex(random_bytes(7));
        }

        if( ! array_key_exists('payment_options', $body) ) {
            $body['payment_options'] = $this->payment_option;
        }

        $this->setBody($body);

        $request = $this->request;
        $request->post( sprintf('%s/payments', $this->getEndpoint()), $body );
        
        if( $request->error ) {
            $this->httpStatusCode = $request->errorCode;
            $this->setMessage();
        }
        else {
            $this->setResponse($request->response);
            $this->setRawResponse($request->getRawResponse());

            $this->httpStatusCode = $request->getHttpStatusCode();

            if ('success' == $request->response->status) {
                $this->status = true;
                $this->checkout_url = $request->response->data->link;
            }

            $this->setMessage($request->response->message);
        }       
    }


    /**
     * Redirect to Flutterwave checkout page
     * 
     * @since 0.5
     */
    public function checkout(): void
    {
        header(sprintf('Location:%s', $this->checkout_url));
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
     * @link https://developer.flutterwave.com/v2.0/docs/rave-standard
     * @since 0.5
     */
    public function verify(string $reference): void
    {
        $this->status = false;

        if(empty($reference)) {
            throw new PayException('Transaction: No reference supplied');
        }

        $this->setBody([
            'reference' => $reference
        ]);

        $request = $this->request;
        $request->get(sprintf('%s/transactions/%s/verify', $this->endpoint, $reference));

        if($request->error) {
            $this->httpStatusCode = $request->errorCode;
            $this->setMessage();
        }
        else {
            $this->setResponse($request->response);
            $this->setRawResponse($request->getRawResponse());

            $this->amount = $request->response->data->amount;

            $this->httpStatusCode   = $request->getHttpStatusCode();
            $this->setMessage($request->response->message);

            if('successful' == $request->response->data->status) {
                $this->status = true;
                $this->setMessage($request->response->data->processor_response);
            }
        }

    }

    /**
     * Handle webhook
     * 
     * When a transaction is made on Flutterwave, Flutterwave sends a payload of
     * data to URL you specify on your dashboard.
     * 
     * This method handle the payload and return TRUE|FALSE for a 
     * valid or non valid transaction.
     * 
     * @link https://developer.flutterwave.com/reference#webhook
     * @since 0.5
     */
    public function webhook(string $secretHash = '') : void
    {
        // Retrieve the request's body
        $body = @file_get_contents("php://input");
        
        $checked = true; // default checked

        // if the has was provided
        if( empty($hashCode) )
        {
            throw new PayException('Secret hash is required. Check Settings/Webhook on your Flutterwave dashboard');
            exit();
        }

        if( ! isset($_SERVER['REQUEST_METHOD']) || strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' )
        {
            throw new PayException('Transaction: Invalid Flutterwave POST request');
            exit();
        }
       
        // Retrieve event signature hash
        $eventHash = (isset($_SERVER['HTTP_VERIF_HASH']) ? $_SERVER['HTTP_VERIF_HASH'] : '');        
                
        // confirm the event's signature hash
        if( $secretHash !== $eventHash )
        {
            $checked = false;
            $this->httpStatusCode = 400;
            $this->message = 'Transaction: Invalid Flutterwave signature';
        }

        if($checked)
        {
            http_response_code(200);
            $this->httpStatusCode = 200;
            $this->setRawResponse($body);

            $response = json_decode($body);

            $this->amount = $response->data->amount;
            $this->setResponse($response);

            if( 'successful' == $response->data->status ) {
                $this->status = true;
                $this->setMessage('Transaction: Charge was completed', 200);
            }
            else {
                $this->setMessage('Transaction: Charge ' . $response->data->status);
            }
        }      
    }
}