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
        Schema::table('permissions', function (Blueprint $table) {
            $existing_selling_price_permissions = DB::table('permissions')
                ->where('name', 'LIKE', '%selling_price_group%');

            if ($existing_selling_price_permissions->exists()) {
                foreach ($existing_selling_price_permissions->get() as $selling_permission) {
                    $explode = explode('.', $selling_permission->name);
                    $new_permission = 'price_group.' . $explode[1];
                    DB::beginTransaction();
                    DB::table('permissions')->where('id', $selling_permission->id)->update(['name' => $new_permission]);
                    DB::commit();
                }
                Log::info('Done writing');
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
        Schema::table('permissions', function (Blueprint $table) {
            //
        });
    }
};
