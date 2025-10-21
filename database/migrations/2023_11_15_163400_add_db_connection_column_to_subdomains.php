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
        Schema::table('subdomains', function (Blueprint $table) {
            if(!Schema::hasColumn('subdomains', 'db_connection')) {
                $table->text('db_connection')->nullable()->after('env_file');
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
        Schema::table('subdomains', function (Blueprint $table) {
            if(Schema::hasColumn('subdomains', 'db_connection')) {
                $table->dropColumn('db_connection');
            }
        });
    }
};
