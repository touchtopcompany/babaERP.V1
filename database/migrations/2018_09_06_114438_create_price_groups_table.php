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
        if(!Schema::hasTable('price_groups')) {
            Schema::create('price_groups', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->integer('business_id')->unsigned();
                $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
                $table->softDeletes();
                $table->timestamps();
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
        Schema::dropIfExists('price_groups');
    }
};
