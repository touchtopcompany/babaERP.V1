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
            if (!Schema::hasColumn('variation_group_prices', 'purchase_price_inc_tax')) {
                $table->decimal('purchase_price_inc_tax', 22, 4)->after('sell_price_inc_tax');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('variation_group_prices', function (Blueprint $table) {
            $table->dropColumn('purchase_price_inc_tax');
        });
    }
};
