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
            'SocialIntegrations',
            function (Blueprint $table) {
                $table->increments('id');
                $table->integer('userId')->unsigned()->nullable();
                $table->string('socialId')->unique();
                $table->timestamp('createdAt');
                $table->foreign('userId')->references('id')->on('Users')->onDelete('CASCADE');
            }
        );
        Schema::table(
            'Users',
            function (Blueprint $table) {
                $table->boolean('hasSocialIntegrations')->default(0)->after('isAdmin');
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
        Schema::drop('SocialIntegrations');
        Schema::table(
            'Users',
            function (Blueprint $table) {
                $table->dropColumn('hasSocialIntegrations');
            }
        );
    }

}
