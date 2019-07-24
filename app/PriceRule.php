<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;

class PriceRule extends Model
{
    protected $fillable = 
    [
	    'price_rule_id',
		'title',
		'target_type',
		'target_selection',
		'allocation_method',
		'value_type',
		'value',
		'customer_selection',
        'allocation_limit',
        'usage_limit',
        'once_per_customer',
        'starts_at',
        'ends_at',
        'entitled_product_ids',
		'entitled_variant_ids',
		'entitled_collection_ids',
		'entitled_country_ids',
		'prerequisite_product_ids',
		'prerequisite_variant_ids',
		'prerequisite_collection_ids',
		'prerequisite_saved_search_ids',
		'prerequisite_customer_ids',
	];



                    
            

	private $api_key;
    private $password;
    private $store_name;

	public function __construct()
	{
		$this->api_key = env('SHOPIFY_KEY','');
        $this->password = env('SHOPIFY_PASSWORD','');
        $this->store_name = env('SHOPIFY_STORE','');
	}

	public function discounts()
	{
		return $this->hasMany('App\Discount', 'price_rule_id','price_rule_id');
	}

	public function getAllPriceRules()
	{
        $url = 'https://'.$this->api_key.':'.$this->password.'@'.$this->store_name.'.myshopify.com/admin/api/2019-07/price_rules.json';      
 
        $client = new Client();

		$res = $client->request('GET', $url, [
		'headers' => [
		'Accept' => 'application/json',
		'Content-type' => 'application/json'
		]]);

		$response = $res->getBody()->getContents();

		$price_rules = json_decode($response)->price_rules;
		return $price_rules;
	}

	public function createPriceRule(Array $price_rule)
	{

        $url = 'https://'.$this->api_key.':'.$this->password.'@'.$this->store_name.'.myshopify.com/admin/api/2019-07/price_rules.json';

        try
        {
        	$client = new Client();

			$response = $client->post($url,
			[
			    'form_params' => $price_rule
			  ]);

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
