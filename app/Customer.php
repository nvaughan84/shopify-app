<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;

class Customer extends Model
{
    protected $fillable = 
    [
    	'shopify_id',
    	'email',
    	'first_name',
    	'last_name',
    	'total_spent',
    	'verified_email',
    	'accepts_marketing'
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
    	return $this->belongsToMany('App\Discount');
    }



    public function getAllShopifyCustomer()
    {

        $all_customers = array();
        $since_id = 0;

        //get customer count
        $url = 'https://'.$this->api_key.':'.$this->password.'@'.$this->store_name.'.myshopify.com/admin/api/2019-07/customers/count.json';
 
        $client = new Client();
        $res = $client->request('GET', $url, [
        'headers' => [
        'Accept' => 'application/json',
        'Content-type' => 'application/json'
        ]]);

        $total = json_decode($res->getBody()->getContents())->count;
        $results_per_request = 250;
        $requests = $total / $results_per_request;

        for($i = 0; $i < $requests; $i++)
        {


             $url = 'https://'.$this->api_key.':'.$this->password.'@'.$this->store_name.'.myshopify.com/admin/api/2019-07/customers/search.json?order=id ASC&query=id:>'.$since_id.'&limit='.$results_per_request;
 
            $client = new Client();
            $res = $client->request('GET', $url, [
            'headers' => [
            'Accept' => 'application/json',
            'Content-type' => 'application/json'
            ]]);

            $customers = json_decode($res->getBody()->getContents())->customers;
            //$since_id = $customers->id;
            $since_id = end($customers)->id;

            
            $all_customers[$i] = $customers;

        }
        
        $merged_customer_array = array_reduce($all_customers, 'array_merge', array());

        return $merged_customer_array;

    }



}


