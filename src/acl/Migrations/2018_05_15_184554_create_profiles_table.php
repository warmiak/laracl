<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration
{
    /**
     * Run the Migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('profilename', 32)->unique();
            $table->string('firstname', 64)->nullable();
            $table->string('lastname', 64)->nullable();
            $table->string('jobtitle', 64)->nullable();
            $table->string('company', 64)->nullable();
            $table->timestamp('birthday')->nullable();
            $table->string('street', 64)->nullable();
            $table->string('street_number', 5)->nullable();
            $table->string('street_additive', 5)->nullable();
            $table->string('postcode', 12)->nullable();
            $table->string('telephone', 24)->nullable();
            $table->string('avatar', 128)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the Migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
