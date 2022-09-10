<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->length(64)->nullable();
            $table->string('code')->length(20);
            $table->tinyInteger('status')->default(1); // (0=expired, 1=active)
            $table->integer('value_percent');
            $table->integer('value_max')->nullable();
            $table->integer('attempts')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discounts');
    }
}
