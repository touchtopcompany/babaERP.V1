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
        Schema::table('pos_removed_product', function (Blueprint $table) {
            $table->renameColumn('product_id', 'variation_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_removed_product', function (Blueprint $table) {
            $table->renameColumn('variation_id', 'product_id');
        });
    }
};
