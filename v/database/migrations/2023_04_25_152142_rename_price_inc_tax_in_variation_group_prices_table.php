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
    public function up(): void
    {
        Schema::table('variation_group_prices', function (Blueprint $table) {
            if (Schema::hasColumn('variation_group_prices', 'price_inc_tax')) {
                $table->renameColumn('price_inc_tax', 'sell_price_inc_tax');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('variation_group_prices', function (Blueprint $table) {
            $table->renameColumn('sell_price_inc_tax', 'price_inc_tax');
        });
    }
};
