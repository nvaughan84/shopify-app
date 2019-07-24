# Discount Email App


# Getting started

## Installation

Please check the official laravel installation guide for server requirements before you start: https://laravel.com/docs/5.8/installation#installation)


Clone the repository

    git clone git@github.com:nvaughan84/shopify-app.git

Switch to the application folder

    cd shopify-app

Install the dependencies using composer

    composer install

Install the dependencies using Node Package Manager

	npm install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Start the local development server

    php artisan serve

You can now access the server at:

	http://localhost:8000


# Setup

Add your SMTP details to the .env file. For example, using mailtrap.io:

	MAIL_DRIVER=smtp
	MAIL_HOST=smtp.mailtrap.io
	MAIL_PORT=2525
	MAIL_USERNAME=xxxxxxxxxxx
	MAIL_PASSWORD=xxxxxxxxxxx
	MAIL_ENCRYPTION=null

Add the Shopify API details to the .env file:

	SHOPIFY_KEY=xxxxxxxxx
	SHOPIFY_PASSWORD=xxxxxxxxxx
	SHOPIFY_STORE=store-subdomain

# Import Data

Rather than seeding the database, data can be imported from the Shopify API. To import Customers, click the 'Sync' button within the Customers tab. This uses the endpoint
	
	GET /api/customers/import 

to load the Shopify Customers into the database.

The import function can be used at any time to import new customers or update existing customers.

Similarly, Price Rules can be imported using the 'Sync' button within the Price Rules tab.

# Emailing Codes

To email the Top Spending customers, the customer list needs to be filtered. To filter the top 10% of spending customers, click the 'Top Customers' button within the Customers tab. 

This runs the endpoint 

	GET api/customers/top-spending 

with the default setting of 10%. 

Once filtered, click the 'Generate and Email Code' button. This passes the current set of customers as a parameter to the endpoint 

	POST api/discounts/create-customer-codes

where a new Price Rule and Unique Code is generated for each of the customers. 

N.B The unique Price Rule uses the endpoint 
	
	POST /admin/api/#{api_version}/price_rules.json
	{
	  "price_rule": {
	    "title": "Email Customer Code",
	    "target_type": "line_item",
	    "target_selection": "all",
	    "allocation_method": "across",
	    "value_type": "fixed_amount",
	    "value": "-10.0",
	    "customer_selection": "all"
	  }
	}

To create a unique Price Rule for each customer the following parameters should be used:

	{
		"customer_selection": "all",
		"prerequisite_customer_ids": 
			[384028349005]
	}

However, this throws an error when passed to the Shopify API.

TODO: Investigate the error so each code can only be used by the specified customer.

# Rest API

## Customers

### GET /customers

Example: /api/customers

Returns all customers stored within the database. These customers can be updated by importing from the Shopify store using the api.


Response body:
													
	[{"id":51,"shopify_id":2062450098262,"first_name":"Kristin","last_name":"Friesen","email":"Kristin.Friesen@developer-tools.shopifyapps.com","total_spent":"138.00","verified_email":1,"accepts_marketing":0,"last_emailed":null,"created_at":"2019-07-05 08:57:46","updated_at":"2019-07-09 14:04:37"}]

### GET /customers/top-spending

Example: /api/customers/top-spending

Returns a list of the top spending Customers as a percentage of the total number of customers. Defaults to 10%. The query string ?percentage={percentage} (e.g ?percentage=20) can be used to specify another percentage

Response body:

	{"message":"Top 10 percent of customers based on total spent","customers":[{"id":55,"shopify_id":2062449967190,"first_name":"Chauncey","last_name":"Zulauf","email":"Chauncey.Zulauf@developer-tools.shopifyapps.com","total_spent":"644.00","verified_email":1,"accepts_marketing":0,"last_emailed":null,"created_at":"2019-07-05 08:57:46","updated_at":"2019-07-09 14:45:41"}}

### GET /customers/import

Used to perform a full synchronisation with the Shopify Customer list. Customers already imported will be updated accordingly.

Repsonse body:


	{"message":"Customers Imported"}

## Price Rules

### GET api/pricerules/import

Used to perform a full synchronisation with the Price Rules within Shopify. Price Rules already imported will be updated accordingly.

### POST api/pricerules

The POST endpoint /api/pricerules is used to create a new Price Rule resource both within the Shopify store and within the App database. 

Example:

	POST /admin/api/{api_version}/price_rules.json 	
	{ 
		"price_rule": 
			{ 
				"title": "Email Customer Code", 															
				"target_type": "line_item", 
				"target_selection": "all", 
				"allocation_method": "across", 
				"value_type": "fixed_amount", 
				"value": "-10.0", 
				"customer_selection": "all" 
			}
		}

### GET /api/pricerules

Retrieve all Price Rules currently stored within the database. 

Response Body:

	[
		{
			"id":102,
			"title":"Email Customer Code",
			"target_type":"line_item",
			"target_selection":"-10.0",
			"value_type":"fixed_amount",
			"value":"-10.0",
			"created_at":"2019-07-09 15:48:07",
			"updated_at":"2019-07-09 15:48:07",
			"allocation_method":"across",
			"price_rule_id":"402180603990"
		}
	]


## Discounts

### POST /api/discounts/create-customer-codes

The endpoint is passed a list of customers in JSON format. For each customer, a new Price Rule is created as well as a unique Discount Code. The Price Rule and Discount Code are stored within Shopify and saved to the database. The code is assigned to a customer within the database table customer_discount.

Once created, the codes are added to an email queue and emailed to the customers.

Todo: Upon creating the code, the Shopify needs to check whether this code exists already within the system using the endpoint:

	GET /admin/api/#{api_version}/discount_codes/lookup.json?code={discount_code}

