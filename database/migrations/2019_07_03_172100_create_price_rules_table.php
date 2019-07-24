<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('price_rule_id');
            $table->string('allocation_method');
            $table->string('title');
            $table->string('target_type');
            $table->string('target_selection');
            $table->string('value_type');
            $table->string('value');            
            $table->string('customer_selection');
            $table->string('allocation_limit')->nullable();
            $table->string('usage_limit')->nullable();

            $table->text('entitled_product_ids')->nullable();
            $table->text('entitled_variant_ids')->nullable();
            $table->text('entitled_collection_ids')->nullable();
            $table->text('entitled_country_ids')->nullable();
            $table->text('prerequisite_product_ids')->nullable();
            $table->text('prerequisite_variant_ids')->nullable();
            $table->text('prerequisite_collection_ids')->nullable();
            $table->text('prerequisite_saved_search_ids')->nullable();
            $table->text('prerequisite_customer_ids')->nullable();



            $table->boolean('once_per_customer')->default(false);
            $table->datetime('starts_at');
            $table->datetime('ends_at');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_rules');
    }
}
