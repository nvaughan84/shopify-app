<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\PriceRule;
use \App\Customer;
use \App\Discount;
use GuzzleHttp\Client;
use \App\Jobs\SendEmailJob;

class DiscountController extends Controller
{
	

    public function index()
    {
    	$discounts = Discount::all();

    	return $discounts;

    	//return $discount->getAllDiscountCodes(401597136982);

    }

    public function createCustomerCodes(Request $request)
    {

    	$customers = $request->input('customers');  


    	$rule = ['price_rule' => [
					    'title' => 'Email Customer Code',
					    'target_type' => 'line_item',
					    'target_selection' => 'all',
					    'allocation_method' => 'across',
					    'value_type' => 'fixed_amount',
					    'value' => '-10',
					    'customer_selection' => 'all',					    
					   	'starts_at' => date('c')
					  ]];

		

    	$discount = new Discount();
    	foreach ($customers as $customer) {

    		/* Note: The following items should make the price rule unique to the customer. However, the endpoint fails with these options*/
    		//$rule['price_rule']['customer_selection'] = 'prerequisite';
    		//$rule['price_rule']['prerequisite_customer_ids'] = [$customer['id']];

    		// need to check against Shopify API that code is unique.
    		$unique_code = $discount->generateCode();

    		$code_id = $this->newCode($unique_code, $rule, $customer['id'], true);
    		
    	}

	
    }

    public function newCode($code, $rule, $customer_id, $send_email = true)
    {

    	$price_rule = new PriceRule();
		$response = $price_rule->createPriceRule($rule);


		$pr = json_decode($response->content())->price_rule;
		$pr = json_decode($pr)->price_rule;


    	$discount = new Discount();
		//$discount_code = $discount->createShopifyDiscountCode($code, $pr->id);
		$discount_code = $discount->createShopifyDiscountCode($code, $pr->id);

		if($discount_code->status() !== 201) 
		{
			$json = [
	            'success' => false,
	            'error' => [
	                'code' => $discount_code->status(),
	                'message' => $discount_code->content(),
	            ],
	        ];

        	return response()->json($json, 400);
		}
		else
		{
			try
			{
				$discount = json_decode($discount_code->content())->discount;
				$response = json_decode($discount)->discount_code;

				$discount = new Discount();
				$discount->code = $response->code;
				$discount->price_rule_id = $pr->id;
				$discount->save();

				$customer = Customer::find($customer_id);
				$customer->discounts()->attach($discount->id);

				if($send_email)
				{
					$details = array();
					$details['email'] = $customer->email;
	    			$details['code'] = $discount->code;;
	    			dispatch(new SendEmailJob($details));
				}

				$json = [
		            'success' => true,
		            'error' => [
		                'code' => 200,
		                'message' => 'Code Successfully Created',
		            ],
		        ];

		        return response()->json($json, 200);
			}
			catch (\Illuminate\Database\QueryException $e) {

		    	$json = [
		            'success' => false,
		            'error' => [
		                'code' => $e->getCode(),
		                'message' => 'Database Error: '.$e->getMessage(),
		            ],
		        ];

        	return response()->json($json, 400);
		        
		    } catch (\Exception $e) {
		        $json = [
		            'success' => false,
		            'error' => [
		                'code' => $e->getCode(),
		                'message' => $e->getMessage(),
		            ],
		        ];

		        return response()->json($json, 400);
		    }		

		}
    }


    public function store(Request $request)
    {
    	$discount_code = $request->discount_code;
    	$price_rule = $request->price_rule;



    	$discount = new Discount();
		$discount_code = $discount->createShopifyDiscountCode($discount_code, $price_rule);


		if($discount_code->status() !== 201) 
		{
			$json = [
	            'success' => false,
	            'error' => [
	                'code' => $discount_code->status(),
	                'message' => $discount_code->content(),
	            ],
	        ];

        	return response()->json($json, 400);
		}
		else
		{
			
			try {
				$discount = json_decode($discount_code->content())->discount;
				$response = json_decode($discount)->discount_code;

				//$pr = PriceRule::where('price_rule_id','=',$response->price_rule_id)->first();

				$discount = new Discount();
				$discount->code = $response->code;
				$discount->price_rule_id = $response->price_rule_id;
				$discount->save();

		    } catch (\Illuminate\Database\QueryException $e) {

		    	$json = [
		            'success' => false,
		            'error' => [
		                'code' => $e->getCode(),
		                'message' => 'Database Error: '.$e->getMessage(),
		            ],
		        ];

        	return response()->json($json, 400);
		        
		    } catch (\Exception $e) {
		        $json = [
		            'success' => false,
		            'error' => [
		                'code' => $e->getCode(),
		                'message' => $e->getMessage(),
		            ],
		        ];

		        return response()->json($json, 400);
		    }

		}
    }
	
    
}
