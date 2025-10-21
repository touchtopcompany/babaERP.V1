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
        // Subdomains table
        if (Schema::hasTable('subdomains')) {
            if (!Schema::hasColumn('subdomains', 'db_connection')) {
                Schema::table('subdomains', function (Blueprint $table) {
                    $table->text('db_connection')->nullable()->after('admin_username');
                });
            }
        }

        // Business table: make weighing_scale_setting nullable
        if (Schema::hasTable('business')) {
            Schema::table('business', function (Blueprint $table) {
                $table->string('weighing_scale_setting')->nullable()->change();
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
        // Subdomains table rollback
        if (Schema::hasTable('subdomains')) {
            if (Schema::hasColumn('subdomains', 'db_connection')) {
                Schema::table('subdomains', function (Blueprint $table) {
                    $table->dropColumn('db_connection');
                });
            }
        }

        // Business table rollback
        if (Schema::hasTable('business')) {
            Schema::table('business', function (Blueprint $table) {
                $table->string('weighing_scale_setting')->nullable(false)->change();
            });
        }
    }
};
