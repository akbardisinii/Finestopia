<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('allocations', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->decimal('primary', 10, 2);
        $table->decimal('secondary', 10, 2);
        $table->decimal('investment', 10, 2);
        $table->decimal('debt', 10, 2);
        $table->decimal('total', 10, 2);
        $table->timestamps();
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allocations');
    }
}