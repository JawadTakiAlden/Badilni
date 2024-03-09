<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
            $table->json('exchanged_item');
            $table->json('my_item')->nullable();
            $table->enum('exchange_type' , ['cash' , 'change']);
            $table->double('price')->nullable();
            $table->double('extra_money')->nullable();
            $table->double('offer_money')->nullable();
            $table->enum('status' , ['pending' , 'rejected' , 'accepted'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchanges');
    }
};
