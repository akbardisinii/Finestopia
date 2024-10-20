<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentOptionsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('title');
            $table->string('desc')->nullable();
            $table->decimal('balance', 10, 2);
            $table->decimal('invest', 10, 2)->default(0);
            $table->boolean('is_deletable')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_options');
    }
}