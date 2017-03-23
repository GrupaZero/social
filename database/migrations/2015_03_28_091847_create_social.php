<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocial extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'social_integrations',
            function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned()->nullable();
                $table->string('social_id')->unique();
                $table->timestamp('created_at');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            }
        );
        Schema::table(
            'users',
            function (Blueprint $table) {
                $table->boolean('has_social_integrations')->default(0)->after('is_admin');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_integrations');
        Schema::table(
            'users',
            function (Blueprint $table) {
                $table->dropColumn('has_social_integrations');
            }
        );
    }

}
