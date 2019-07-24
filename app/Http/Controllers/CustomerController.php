<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use \App\Customer;
use \App\PriceRule;
use Illuminate\Support\Facades\Input;

class CustomerController extends Controller
{

	public function index()
	{
		$customers = Customer::all();

		return $customers->toJson();
	}


	/*
	*
	* Import Customers from the Shopify API into the DB
	*
	*/
    public function import()
    {

    	$customerList = new Customer();
    	$customers = $customerList->getAllShopifyCustomer();

		foreach ($customers as $customer) {
			$cus = Customer::updateOrCreate(
			    ['shopify_id' => $customer->id],
			    [
			    	'email' => $customer->email, 
			    	'first_name' => $customer->first_name,
			    	'last_name' => $customer->last_name,
			    	'total_spent' => $customer->total_spent,
			    	'verified_email' => $customer->verified_email,
			    	'accepts_marketing' => $customer->accepts_marketing,
			    	'shopify_id' => $customer->id
			    ]
			);
		}

		return response()->json([
	            'message' => 'Customers Imported'
	        ], 200); 
    }

    /*
    *	Return the top customers based on total spend
    *	The percentage can be passed as a GET variable or defaults to 10
    *
    */
	public function topSpending($percentage = 10)
	{
		$percentage =  Input::has('percentage') ? Input::get('percentage') : $percentage;

		//$ignoreMarketing = Input::get('ignore_marketing')!==null ? Input::get('ignore_marketing') : 1;
		//echo $ignoreMarketing;
		//retrieve the total number of customers in the database
		$total = Customer::count();

		//Order by total spent and retrieve the top x percent of these users
		$topCustomers = Customer::where('total_spent','>',0)->orderBy('total_spent', 'desc')
	               ->take(ceil($percentage/100*$total));
	    $topCustomers = $topCustomers->get();

	    if($topCustomers->count() == 0)
	    {
	    	return response()->json([
	            'message' => 'There are no results to return'
	        ], 200);
	    }

	    return response()->json([
            'message' => 'Top '.$percentage.' percent of customers based on total spent',
            'customers' => $topCustomers
        ], 200);

	    return $topCustomers;
	}


}
