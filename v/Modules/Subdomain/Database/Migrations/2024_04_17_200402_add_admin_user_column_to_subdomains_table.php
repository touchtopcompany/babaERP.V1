<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            if (!$connection->hasColumn('subdomains', 'admin_username')) {
                $connection->table('subdomains', function (Blueprint $table) {
                    $table->string('admin_username')->unique()->after('active_modules');
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
            $table->dropColumn('admin_user');
        });
    }
};
