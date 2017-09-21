<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email',100)->unique();
            $table->string('password');
            $table->string('signup_with');
            $table->integer('device_id')->unsigned();
            $table->integer('country_id')->unsigned()->nullable();
            $table->string('gender');
            $table->string('phone')->nullable();
            $table->date('dob');
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->string('profile_pic')->nullable();
            $table->boolean('active');
            $table->string('remember_token')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
