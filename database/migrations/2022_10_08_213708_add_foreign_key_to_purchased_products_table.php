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
        Schema::table('purchased_products', function (Blueprint $table) {
            //
            $table->foreign("order_id")->references("id")->on("orders")->onDelete("cascade");
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchased_products', function (Blueprint $table) {
            //
            $table->dropForeign("purchased_products_order_id_foreign");
        });
    }
};
