<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('pos_removed_product')) {
            Schema::create('pos_removed_product', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('product_name', 191);
                $table->integer('product_id');
                $table->float('amount_removed');
                $table->integer('quantity_removed');
                $table->bigInteger('removed_by');
                $table->integer('location_id');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos_removed_product');
    }
};
