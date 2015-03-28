<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

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
                $table->string('socialId')->unique();
                $table->timestamp('createdAt');
                $table->timestamp('updatedAt');
            }
        );
        Schema::table(
            'Users',
            function (Blueprint $table) {
                $table->boolean('hasSocialIntegrations')->default(0)->after('id');
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
