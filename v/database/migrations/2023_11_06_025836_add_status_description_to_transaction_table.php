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
            if(!Schema::hasColumn('transactions','status_description')){
                $table->text('status_description')->nullable()->after('user_changes');
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
        Schema::table('transaction', function (Blueprint $table) {
            if(Schema::hasColumn('transactions','status_description')){
                $table->dropColumn('status_description');
            }
        });
    }
};
