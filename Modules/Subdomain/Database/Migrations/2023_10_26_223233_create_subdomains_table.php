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
        if(!$connection->hasTable('subdomains')) {
            $connection->create('subdomains', function (Blueprint $table) {
                $table->string('id');
                $table->string('sub_domain');
                $table->integer('registered_by');
                $table->timestamps();
                $table->softDeletes();
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
        Schema::dropIfExists('subdomains');
    }
};
