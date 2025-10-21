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
        // \Illuminate\Support\Facades\DB::transaction(function () {

            if(!Schema::hasColumn('business_locations', 'common_settings')) {
                Schema::table('business_locations', function (Blueprint $table) {
                    $table->string('common_settings')->after('is_active')->nullable();
                });
            }

            //Selling was done using different currency
            if(!Schema::hasColumn('transactions', 'for_curr')) {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->integer('for_curr')->after('type')->nullable();
                });
            }

            if(!Schema::hasTable('multi_currencies_settings')) {
                Schema::create('multi_currencies_settings', function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->integer('business_id');
                    $table->integer('currency_id');
                    $table->decimal('exchange_rate', 22, 6)->default(1);
                    $table->enum('exchange_rate_type', ['api', 'fixed'])->default('fixed');
                    $table->timestamps();
                });
            }
        // });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_locations', function (Blueprint $table) {
            $table->dropColumn('common_settings');
        });

        Schema::dropIfExists('multi_currencies_settings');
    }
};
