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
        $connection =  Schema::connection('core_domain');
        if($connection->hasTable('subdomains')) {
            $connection->table('subdomains', function (Blueprint $table) use($connection) {
                if ($connection->hasColumn('subdomains', 'database') && !($connection->hasColumn('subdomains', 'db_name'))) {
                    $table->renameColumn('database', 'db_name');
                }
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
        Schema::table('subdomains', function (Blueprint $table) {
            if(Schema::hasColumn('subdomains', 'db_name')) {
                $table->renameColumn('db_name', 'database');
            }
        });
    }
};
