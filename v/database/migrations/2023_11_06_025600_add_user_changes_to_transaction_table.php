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
        Schema::table('transactions', function (Blueprint $table) {
            if(!Schema::hasColumn('transactions','user_changes')){
                $table->text('user_changes')->nullable()->after('selling_price_group_id');
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
        Schema::table('transactions', function (Blueprint $table) {
            if(Schema::hasColumn('transactions','user_changes')){
                $table->dropColumn('user_changes');
            }
        });
    }
};
