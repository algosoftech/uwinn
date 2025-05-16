<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Stripe_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct(); 
	}

	 

	/***********************************************************************
	** Function name 	: getCategoryList
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for get Category
	** Date 			: 16 APRIL 2025
	************************************************************************/
	public function paymentIntent($intentData)
	{
	    $params = array(
	        "payment_mode"      =>  STRIPE_PAYMENT_MODE,
	        "private_live_key"  =>  STRIPE_LIVE_SK,
	        "public_live_key"   =>  STRIPE_LIVE_PK,
	        "private_test_key"  =>  STRIPE_TEST_SK,
	        "public_test_key"   =>  STRIPE_TEST_PK
	    );

	    $privateKey = $params['payment_mode'] === "test"  ? $params['private_test_key'] : $params['private_live_key'];

	    $returnArray = array();

	    try {
    		if ($intentData['mobile'])    $requestData['description'] = 'Created payment tranaction for ( '.$intentData['mobile'].') '. $intentData['tranasactionID'];
			if ($intentData['amount'])    $requestData['amount']      = $intentData['amount']*100;
			if ($intentData['currency'])  $requestData['currency']    = $intentData['currency'];
			if ($intentData['metadata'])  $requestData['metadata']    = $intentData['metadata'];
			
		 	$requestData['automatic_payment_methods']  = array('enabled' => true);
		 	// $requestData['payment_method_types']        = array('card');
		 	$options1['customer']            = $intentData['customer'];
		 	$options1['setup_future_usage']  = $intentData['off_session'];
		 	$options['idempotency_key']      = $intentData['idempotencyKEY'];

	        // Create a PaymentIntent with amount and currency
	    	$stripe 	   = new \Stripe\StripeClient($privateKey);
	        $paymentIntent = $stripe->paymentIntents->create($requestData,$options,$options1);

	        $ephemeralKey = $this->ephemeralKeys($intentData['customer']);
	        // $ephemeralKey = $this->ephemeralKeys('cus_S8hxUco9zf3BzT');

	        $returnArray['status']           = 'success';
	        $returnArray['paymentIntent_id'] = $paymentIntent->id;
	        $returnArray['client_secret']    = $paymentIntent->client_secret;
	        $returnArray['description']      = $paymentIntent->description;
	        $returnArray['currency']         = $paymentIntent->currency;
	        $returnArray['amount']           = $paymentIntent->amount /100;
	        $returnArray['EphemeralKey']     = $ephemeralKey['secret'];
	        $returnArray['customerID']       = $intentData['customer'];
	        $returnArray['tranasactionID']   = $intentData['tranasactionID'];

	    } catch (\Stripe\Exception\CardException $e) {
	        // Handle card-related errors
	        $returnArray['status']  = 'error';
	        $returnArray['message'] = 'Card error: ' . $e->getMessage();
	    } catch (\Stripe\Exception\RateLimitException $e) {
	        // Handle rate limit errors
	        $returnArray['status']  = 'error';
	        $returnArray['message'] = 'Rate limit error: ' . $e->getMessage();
	    } catch (\Stripe\Exception\InvalidRequestException $e) {
	        // Handle invalid request errors
	        $returnArray['status']  = 'error';
	        $returnArray['message'] = 'Invalid request: ' . $e->getMessage();
	    } catch (\Stripe\Exception\AuthenticationException $e) {
	        // Handle authentication errors
	        $returnArray['status']  = 'error';
	        $returnArray['message'] = 'Authentication error: ' . $e->getMessage();
	    } catch (\Stripe\Exception\ApiConnectionException $e) {
	        // Handle network errors
	        $returnArray['status']  = 'error';
	        $returnArray['message'] = 'Network error: ' . $e->getMessage();
	    } catch (\Stripe\Exception\ApiErrorException $e) {
	        // Handle general API errors
	        $returnArray['status']  = 'error';
	        $returnArray['message'] = 'API error: ' . $e->getMessage();
	    } catch (Exception $e) {
	        // Handle non-Stripe exceptions
	        $returnArray['status']  = 'error';
	        $returnArray['message'] = 'General error: ' . $e->getMessage();
	    }

	    return $returnArray;
	}

	/***********************************************************************
	** Function name 	: createCustomers
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for createCustomers
	** Date 			: 16 APRIL 2025
	************************************************************************/
	public function createCustomers($customerData)
	{
	    
	    try {

	    	$params = array(
		        "payment_mode"      =>  STRIPE_PAYMENT_MODE,
		        "private_live_key"  =>  STRIPE_LIVE_SK,
		        "public_live_key"   =>  STRIPE_LIVE_PK,
		        "private_test_key"  =>  STRIPE_TEST_SK,
		        "public_test_key"   =>  STRIPE_TEST_PK
		    );

		    $privateKey = $params['payment_mode'] === "test"  ? $params['private_test_key'] : $params['private_live_key'];

		    $createCustomer['name']  = $customerData['users_name'].' '.$customerData['last_name'];
		    $createCustomer['email'] = $customerData['email'];
		    $createCustomer['phone'] = $customerData['country_code'].$customerData['users_mobile'];
		    $createCustomer['email'] = $customerData['users_email'];
		   
		    $options['idempotency_key'] = $customerData['users_id'];
		    $stripe         = new \Stripe\StripeClient($privateKey);
	        $createCustomer = $stripe->customers->create($createCustomer,$options);
	        // $customersList = $stripe->customers->all(['limit' => 3]);

	        // Storing customer id to the uw_users collection.
	        $tableName                           = 'uw_users';
	        $updateParam['stripe_customer_id']   = $createCustomer['id'];
	      	$this->common_model->editData($tableName,$updateParam ,'users_id', (int)$customerData['users_id']);
	      	// echo "<pre>";print_r($createCustomer);die();
	      	$returnArray['customer_id']     = $createCustomer['id'];
	      	$returnArray['invoice_prefix']  = $createCustomer['invoice_prefix'];
	      	$returnArray  = $createCustomer;
 
	    } catch (Exception $e) {
	        // Handle non-Stripe exceptions
	        $returnArray['status']  = 'error';
	        $returnArray['message'] = 'General error: ' . $e->getMessage();
	    }

	    return $returnArray;
	}

	/***********************************************************************
	** Function name 	: ephemeralKeys
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for ephemeralKeys
	** Date 			: 16 APRIL 2025
	************************************************************************/
	public function ephemeralKeys($customerData)
	{
	  try {

	    	$params = array(
		        "payment_mode"      =>  STRIPE_PAYMENT_MODE,
		        "private_live_key"  =>  STRIPE_LIVE_SK,
		        "public_live_key"   =>  STRIPE_LIVE_PK,
		        "private_test_key"  =>  STRIPE_TEST_SK,
		        "public_test_key"   =>  STRIPE_TEST_PK
		    );

		    $privateKey        			   = $params['payment_mode'] === "test"  ? $params['private_test_key'] : $params['private_live_key'];
		    $nonce['customer']             =  $customerData;
			$requestData['stripe_version'] = '2023-10-16';

			// See your keys here: https://dashboard.stripe.com/apikeys
			$stripe 	  = new \Stripe\StripeClient($privateKey);
		    $ephemeralKey = $stripe->ephemeralKeys->create($nonce,$requestData);
	      	$returnArray  = $ephemeralKey;
 
	      } catch (Exception $e) {
	        // Handle non-Stripe exceptions
	        $returnArray['status']  = 'error';
	        $returnArray['message'] = 'General error: ' . $e->getMessage();
	      }

	    return $returnArray;

	}
}