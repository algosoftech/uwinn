<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(0);
		$this->load->model('common_model');
	} 

	public function index()
	{

		$edcoded_id = $_GET['id'];
      
      	if(empty($edcoded_id)):
      		header("Location: https://www.dealzarabia.com/checkout");
      	endif;
      
		$this->session->set_userdata('DEALZOID' ,$edcoded_id);

		$id = base64_decode($edcoded_id);
		$araryID = explode('___', $id);

		$id = $araryID[1];


		$table = 'orders';
		$where['id'] = $id;
		$data['orderData']  = $this->common_model->get($table,$where,'');

		$this->load->view('payment',$data);		
		// $this->load->view('product_form');		
	}

	public function check()
	{
		//check whether stripe token is not empty
		if(!empty($_POST['stripeToken']))
		{

			//get token, card and user info from the form
			$token  = $_POST['stripeToken'];
			$name = $_POST['name'];
			$email = $_POST['email'];
			$card_num = $_POST['card_num'];
			$card_cvc = $_POST['cvc'];
			$card_exp_month = $_POST['exp_month'];
			$card_exp_year = $_POST['exp_year'];

			
			$edcoded_id =$this->session->userdata('DEALZOID');
			$id = base64_decode($edcoded_id);
			$araryID = explode('___', $id);

			 $id = $araryID[1];

			 $table = 'orders';
			 $where['id'] = $id;
			 $select = "id,order_id";
			 $data['orderData']  = $this->common_model->get($table,$where,$select);
				
			//include Stripe PHP library
			require_once APPPATH."third_party/stripe/init.php";
			
			//set api key
			$stripe = array(
			  "secret_key"      => STRIPE_TEST_SK,
			  "publishable_key" => STRIPE_TEST_PK
			);


			
			\Stripe\Stripe::setApiKey($stripe['secret_key']);
			
			//add customer to stripe
			$customer = \Stripe\Customer::create(array(
				'email' => $email,
				'source'  => $token
			));
			
			//item information
			$itemName = "Stripe Donation";
			$itemNumber = "Payment for orderId " .$data['orderData'][0]['order_id'];
			$itemPrice = $_POST['inclusice_of_vat']*100;
			$currency = "aed";
			$orderID = $data['orderData'][0]['order_id'];
			
			//charge a credit or a debit card
			$charge = \Stripe\Charge::create(array(
				'customer' => $customer->id,
				'amount'   => $itemPrice,
				'currency' => $currency,
				'description' => $itemNumber,
				'metadata' => array(
					'item_id' => $itemNumber
				)
			));

			//retrieve charge details
			$chargeJson = $charge->jsonSerialize();

			//check whether the charge is successful
			if($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1)
			{
				//order details 
				$amount = $chargeJson['amount'];
				$balance_transaction = $chargeJson['balance_transaction'];
				$currency = $chargeJson['currency'];
				$status = $chargeJson['status'];
				$date = date("Y-m-d H:i:s");
			
				
				//insert tansaction data into the database
				$dataDB = array(
					'name' => $name,
					'email' => $email,
					'card_num' => $card_num, 
					'card_cvc' => $card_cvc, 
					'card_exp_month' => $card_exp_month, 
					'card_exp_year' => $card_exp_year, 
					'item_name' => $itemName, 
					'item_number' => $itemNumber, 
					'item_price' => $itemPrice, 
					'item_price_currency' => $currency, 
					'paid_amount' => $amount, 
					'paid_amount_currency' => $currency, 
					'txn_id' => $balance_transaction, 
					'order_status' => 'success',
					'payment_status' => $status,
					'created' => $date,
					'modified' => $date
				);

				try {

					$table = 'orders';
					$where['id'] = $id;

					$result = $this->common_model->update($table, $dataDB,$where);

					if($result) {
						$response['insertID'] = $result;
						$response['status'] = 'success';

						header("Location: https://www.dealzarabia.com/order-success/");
						// echo json_encode($response);
					}else{
						echo "Error";
					}

				} catch (Exception $e) {
					echo 'db error';die();	
				}
			}
			else
			{
				echo "Invalid Token";
				$statusMsg = "";
			}
		}
	}

	public function payment_success()
	{
		$this->load->view('payment_success');
	}

	public function payment_error()
	{
		$this->load->view('payment_error');
	}

	public function help()
	{
		$this->load->view('help');
	}

	public function order()
	{
			$ORparam["sequence_id"]	 = $this->input->post('sequence_id');
			$ORparam["order_id"]	 = $this->input->post('order_id');
			$ORparam["product_count"]	 = $this->input->post('product_count');
			$ORparam["finaltotal"]	 = $this->input->post('finaltotal');
			$ORparam["order_status"]	 = $this->input->post('order_status');
			// $ORparam['status'] = 'inizilize';
			$ORparam["payment_from"]	 = $this->input->post('payment_from');
			$ORparam["creation_ip"]	 = $this->input->post('creation_ip');
			// $ORparam["created_at"]	 = $this->input->post('created_at');
			try {
				$this->db->insert('orders', $ORparam);
				$insert_id = $this->db->insert_id();
				if($insert_id) {
					$data['insertID'] = $insert_id;

					$response['insertID'] = $data['insertID'];
					$response['status'] = 'success';
					echo json_encode($response);
				}else{
					echo "Error";
				}
			} catch (Exception $e) {
				echo 'db error';die();	
			}
			
	}
}
