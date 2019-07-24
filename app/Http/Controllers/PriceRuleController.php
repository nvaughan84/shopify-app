<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\PriceRule;

class PriceRuleController extends Controller
{
    public function store(Request $request)
    {

 		$rule = ['price_rule' => [
					    'title' => $request->title,
					    'target_type' => $request->target_type,
					    'target_selection' => 'all',
					    'allocation_method' => $request->allocation_method,
					    'value_type' => $request->value_type,
					    'value' => $request->value,
					    'customer_selection' => 'all',
					   	'starts_at' => date('c')
					  ]];

		$price_rule = new PriceRule();
		$response = $price_rule->createPriceRule($rule);


		if($response->status() !== 201) 
		{
			return $response;
		}
		else
		{
			$pr = json_decode($response->content())->price_rule;
			$pr = json_decode($pr)->price_rule;

			$price_rule = new PriceRule();

			// $price_rule->price_rule_id = $pr->id;
	  //   	$price_rule->title = $pr->title;
			// $price_rule->target_type = $pr->target_type;
			// $price_rule->target_selection = $pr->target_selection;
			// $price_rule->allocation_method = $pr->allocation_method;
			// $price_rule->value_type = $pr->value_type;
			// $price_rule->value = $pr->value;
			// $price_rule->target_selection = $pr->target_selection;

			$starts_at = $pr->starts_at;
			$starts_at = date('Y-m-d H:i:s', strtotime($starts_at));
			$ends_at = $pr->ends_at;
			$ends_at = date('Y-m-d H:i:s', strtotime($ends_at));

			$price_rule->price_rule_id = $pr->id;
			$price_rule->title = $pr->title;
			$price_rule->target_type = $pr->target_type;
			$price_rule->target_selection = $pr->target_selection;
			$price_rule->allocation_method = $pr->allocation_method;
			$price_rule->value_type = $pr->value_type;
			$price_rule->value = $pr->value;
			$price_rule->customer_selection = $pr->customer_selection;
			$price_rule->allocation_limit = $pr->allocation_limit;
			$price_rule->usage_limit = $pr->usage_limit;
			$price_rule->once_per_customer = $pr->once_per_customer;
			$price_rule->starts_at = $starts_at;
			$price_rule->ends_at = $ends_at;
			$price_rule->entitled_product_ids = serialize($pr->entitled_product_ids);
			$price_rule->entitled_variant_ids = serialize($pr->entitled_variant_ids);
			$price_rule->entitled_collection_ids = serialize($pr->entitled_collection_ids);
			$price_rule->entitled_country_ids = serialize($pr->entitled_country_ids);
			$price_rule->prerequisite_product_ids = serialize($pr->prerequisite_product_ids);
			$price_rule->prerequisite_variant_ids = serialize($pr->prerequisite_variant_ids);
			$price_rule->prerequisite_collection_ids = serialize($pr->prerequisite_collection_ids);
			$price_rule->prerequisite_saved_search_ids = serialize($pr->prerequisite_saved_search_ids);
			$price_rule->prerequisite_customer_ids = serialize($pr->prerequisite_customer_ids);



			$price_rule->save();

			return $price_rule;
		}

    }

    public function index()
    {
    	$price_rules = PriceRule::all();

    	return $price_rules->toJson();
    }

    public function import()
    {
    	$price_rule_list = new PriceRule();
    	$price_rules = $price_rule_list->getAllPriceRules();

		foreach ($price_rules as $rule) {
			$starts_at = $rule->starts_at;
			$starts_at = date('Y-m-d H:i:s', strtotime($starts_at));
			$ends_at = $rule->ends_at;
			$ends_at = date('Y-m-d H:i:s', strtotime($ends_at));
			$price_rule = PriceRule::updateOrCreate(
			    ['price_rule_id' => $rule->id],
			    [
			    	'title' => $rule->title,
					'target_type' => $rule->target_type,
					'target_selection' => $rule->target_selection,
					'allocation_method' => $rule->allocation_method,
					'value_type' => $rule->value_type,
					'value' => $rule->value,
					'target_selection' => $rule->value,
					'price_rule_id' => $rule->id,
					'customer_selection' => $rule->customer_selection,
			        'allocation_limit' => $rule->allocation_limit,
			        'usage_limit' => $rule->usage_limit,
			        'once_per_customer' => $rule->once_per_customer,
			        'starts_at' => $starts_at,
			        'ends_at' => $ends_at,
			        'entitled_product_ids' => serialize($rule->entitled_product_ids),
					'entitled_variant_ids' => serialize($rule->entitled_variant_ids),
					'entitled_collection_ids' => serialize($rule->entitled_collection_ids),
					'entitled_country_ids' => serialize($rule->entitled_country_ids),
					'prerequisite_product_ids' => serialize($rule->prerequisite_product_ids),
					'prerequisite_variant_ids' => serialize($rule->prerequisite_variant_ids),
					'prerequisite_collection_ids' => serialize($rule->prerequisite_collection_ids),
					'prerequisite_saved_search_ids' =>serialize($rule->prerequisite_saved_search_ids),
					'prerequisite_customer_ids' => serialize($rule->prerequisite_customer_ids),
			    ]
			);
		}


		return json_encode($price_rules);
    }


	public function destroy($price_rule_id)
	{
		
		try
        {
        	$url = 'https://'.$this->api_key.':'.$this->password.'@'.$this->store_name.'.myshopify.com/admin/api/2019-07/price_rules/'.$price_rule_id.'.json';
        	$client = new Client();

			$response = $client->delete($url);

			$json = [
	            'success' => true,
	            'price_rule' => $response->getBody()->getContents()
	        ];

        	return response()->json($json, 201);

        }
        catch(\GuzzleHttp\Exception\RequestException $e)
        {
        	
        	$error = $e->getCode();

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
