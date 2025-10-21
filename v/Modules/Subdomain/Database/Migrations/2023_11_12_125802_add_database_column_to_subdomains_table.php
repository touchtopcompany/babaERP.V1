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
        $connection = Schema::connection('core_domain');
        if ($connection->hasTable('subdomains')) {
            if (!$connection->hasColumn('subdomains', 'database')) {
                $connection->table('subdomains', function (Blueprint $table) {
                    $table->string('database')->after('sub_domain');
                });
            }
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
            $table->dropColumn('database');
        });
    }
};
