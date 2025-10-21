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
        Schema::table('variation_group_prices', function (Blueprint $table) {
            $table->decimal('sell_price_inc_tax',22, 4)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('variation_group_price', function (Blueprint $table) {
            $table->dropColumn('selling_price_inc_tax');
        });
    }
};
