<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{

    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->length(64);
            $table->string('name');
            $table->string('username');
            $table->string('password');
            $table->json('information');
            $table->string('role')->length(10); // (admin, customer)
            $table->tinyInteger('level'); // (0=inactive, 1=active)
            $table->rememberToken();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
    
}
