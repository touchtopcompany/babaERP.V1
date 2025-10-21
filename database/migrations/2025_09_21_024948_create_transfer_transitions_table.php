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

        Schema::create('transfer_transitions', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('transfer_id');
            $t->string('action', 32);
            $t->timestamps();
            $t->unique(['transfer_id', 'action']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_transitions');
    }
};
