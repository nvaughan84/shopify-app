<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('admin.dashboard');
});


Route::get('api/customers/top-spending', 'CustomerController@topSpending');
Route::get('api/customers/import', 'CustomerController@import');
Route::resource('api/customers', 'CustomerController');


Route::get('api/pricerules/import', 'PriceRuleController@import');
Route::resource('api/pricerules', 'PriceRuleController');

Route::post('api/discounts/create-customer-codes','DiscountController@createCustomerCodes');
Route::get('api/discounts/new-code','DiscountController@newCode');
Route::resource('api/discounts','DiscountController');



