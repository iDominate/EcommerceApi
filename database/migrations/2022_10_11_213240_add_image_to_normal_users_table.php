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
        Schema::table('normal_users', function (Blueprint $table) {
            //
            $table->string("image")->default("default_image.jpg");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('normal_users', function (Blueprint $table) {
            //
            $table->dropColumn("image");
        });
    }
};
