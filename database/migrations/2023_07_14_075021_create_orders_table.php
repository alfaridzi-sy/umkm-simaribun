<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->string('order_code');
            $table->date('order_date');
            $table->string('price_amount')->nullable();
            $table->string('order_status');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('shipping_address');
            $table->string('subdistrict');
            $table->string('district');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code');
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
        Schema::dropIfExists('orders');
    }
}
