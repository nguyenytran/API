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
            $table->bigIncrements('id');
            $table->unsignedInteger('role_id')->index();
            $table->string('name');
            $table->string('username', 100)->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->unsignedTinyInteger('gender')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 45)->nullable();
            $table->string('profile_picture')->nullable();
            $table->tinyInteger('is_active')->default(0);
            $table->unsignedTinyInteger('created_by');
            $table->timestamps();
            $table->softDeletes();
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
