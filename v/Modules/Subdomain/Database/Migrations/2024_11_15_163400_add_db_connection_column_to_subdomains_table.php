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
                if (!$connection->hasColumn('subdomains', 'db_connection')) {
                    $table->text('db_connection')->nullable()->after('admin_username');
                }
            });
        }

        //Set weighingscale to null
        Schema::table('business', function (Blueprint $table) {
            $table->string('weighing_scale_setting')->nullable()->change();
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
