<?php declare(strict_types=1);

namespace PayWith\Processors\Paystack;

use PayWith\Helpers\DoSomething;
use PayWith\Exceptions\PayException;
use PayWith\Processors\Paystack\Paystack;

class Transaction extends Paystack
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
     * Checkout url
     * 
     * @var string
     * @since 0.5
     */
    protected $checkout_url;

    /**
     * Transaction dafault currency 
     * 
     * @var array
     * @since 0.5
     */
    protected $currency = 'NGN';

    /**
     * Fallback if payment option not set
     * 
     * @var string
     * @since 0.5
     */
    protected $payment_option = 'card';

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
    public function initialize(array $body = []) : void
    {
        if( ! array_key_exists('amount', $body) ) {
            throw new PayException('Amount is required for transaction');
        } 

        if( ! array_key_exists('email', $body) ) {
            throw new PayException('Email is required for transaction');
        } 

        $this->setBody($body);

        $request = $this->request;
        $request->post( sprintf('%s/initialize', $this->getEndpoint()), $body );
        
        if($request->error) {
            $this->httpStatusCode = $request->errorCode;
            $this->setMessage();
        }
        else {
            $this->setResponse($request->response);
            $this->setRawResponse($request->getRawResponse());

            $this->httpStatusCode = $request->getHttpStatusCode();

            if (true == $request->response->status) {
                $this->status = true;
                $this->checkout_url = $request->response->data->authorization_url;
            }

            $this->setMessage($request->response->message);
        }
    }

    /**
     * Redirect to paystack checkout page
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
     * @link https://paystack.com/docs/api/#transaction-verify
     * @since 0.5
     */
    public function verify(string $reference): void
    {
        $this->status = false;

        if( empty($reference) ) {
            throw new PayException('Transaction: No reference supplied');
        }

        $this->setBody([
            'reference' => $reference
        ]);

        $request = $this->request;
        $request->get( sprintf('%s/verify/%s', $this->endpoint, $reference) );

        if( $request->error ) {
            $this->httpStatusCode = $request->errorCode;
            $this->setMessage();
        }
        else {
            $this->setResponse($request->response);
            $this->setRawResponse($request->getRawResponse());

            $this->amount = $request->response->data->amount;
            $this->httpStatusCode   = $request->getHttpStatusCode();

            if( 'success' == $request->response->data->status )
            {
                $this->status = true;
            }

            $this->setMessage($request->response->message);
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
     * @link https://paystack.com/docs/payments/webhooks/
     * @since 0.5
     */
    public function webhook(): void
    {
        $checked = true; // default checked

        // only a post with paystack signature header gets our attention
        if( (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' ) /*|| !array_key_exists('x-paystack-signature', $_SERVER) */ )
        {
            $checked = false;

            $this->httpStatusCode = 400;
            $this->message= 'Transaction: Invalid POST signature';
        }

        if ($checked) {
            // Retrieve the request's body
            $input = @file_get_contents("php://input");

            // validate event do all at once to avoid timing attack
            if( ($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '') !== hash_hmac('sha512', $input, $this->getSecretKey()) )
            {
                $checked = false;

                $this->httpStatusCode = 401;
                $this->message = 'Transaction: Invalid event signature';
            }
            
            if ($checked) {
                http_response_code(200);
                
                $this->httpStatusCode = 200;
                $this->setRawResponse($input);
                
                // parse event (which is json string) as object
                // Do something - that will not take long - with $event
                $event = json_decode($input);

                if ('charge.success' == ($event->event ?? '') ) {
                    $this->amount = $event->data->amount;
                    $this->setResponse($event);
                    $this->setMessage($event->data->gateway_response);

                    if ('success' == $event->data->status) {
                        $this->status = true;
                    }
                }
                else {
                    $this->message = 'Transaction: Paystack charge.failed';
                }
            }
        }
    }
}