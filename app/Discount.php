<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;

class Discount extends Model
{

	private $api_key;
    private $password;
    private $store_name;

	public function __construct()
	{
		$this->api_key = env('SHOPIFY_KEY','');
        $this->password = env('SHOPIFY_PASSWORD','');
        $this->store_name = env('SHOPIFY_STORE','');
	}

    public function customers()
    {
        return $this->belongsToMany('App\Customer');
    }

    public function priceRule()
    {
    	return $this->belongsTo('App\priceRule', 'price_rule_id', 'price_rule_id');
    }

    public function findCode($code)
    {
        $url = 'https://'.$this->api_key.':'.$this->password.'@'.$this->store_name.'.myshopify.com/admin/api/2019-07/discount_codes/lookup.json?code='.$code;


            $client = new Client();
            $res = $client->request('GET', $url, [
            'headers' => [
            'Accept' => 'application/json',
            'Content-type' => 'application/json'
            ]]);

            return $res->getStatusCode();
    }

    public function getAllDiscountCodes($price_rule)
    {

    	try
    	{
	    	$url = 'https://'.$this->api_key.':'.$this->password.'@'.$this->store_name.'.myshopify.com/admin/api/2019-07/price_rules/'.$price_rule.'/discount_codes.json';

	    	$client = new Client();
			$res = $client->request('GET', $url, [
			'headers' => [
			'Accept' => 'application/json',
			'Content-type' => 'application/json'
			]]);

			return json_decode($res->getBody()->getContents())->discount_codes;
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

    /*
    *	returns a random string containing uppercase letters and numbers. Need to check if code is already in use
    */
    public function generateCode()
    {

    	$code = $this->randomString();

    	return $code;

    	/* NOTE: Checking against the Shopify API to determine whether the code is in use fails!  */

		//$url = 'https://'.$this->api_key.':'.$this->password.'@'.$this->store_name.'.myshopify.com/admin/api/2019-07/discount_codes/lookup.json?code='.$code;

		//$client = new Client();
		// $res = $client->request('GET', $url);

		// print_r($res->getBody()->getContents());
    }

    public function randomString($length = 12)
    {
    	
    	return substr(str_shuffle(str_repeat($x='0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);

    }

    public function createShopifyDiscountCode($code, $price_rule)
    {

        $url = 'https://'.$this->api_key.':'.$this->password.'@'.$this->store_name.'.myshopify.com/admin/api/2019-07/price_rules/'.$price_rule.'/discount_codes.json';

        $discount_code = ['discount_code' => [
            'code' => $code
        ]];

        try
        {
        	$client = new Client();
        	$response = $client->post($url,['form_params' => $discount_code]);

        	$json = [
	            'success' => true,
	            'discount' => $response->getBody()->getContents()
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
