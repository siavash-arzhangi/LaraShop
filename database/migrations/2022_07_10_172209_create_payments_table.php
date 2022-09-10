<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->length(64);
            $table->string('invoice_id')->length(64);
            $table->integer('value');
            $table->integer('status')->length(4)->default(0); // (0=pending, 100=success, 99=fail, 101=success but failed)
            $table->text('code')->nullable(); // transaction code
            $table->string('gateway');
            $table->json('information')->nullable(); // bank, card number, ip
            $table->dateTime('paid_at')->nullable();
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
        Schema::dropIfExists('payments');
    }
}
