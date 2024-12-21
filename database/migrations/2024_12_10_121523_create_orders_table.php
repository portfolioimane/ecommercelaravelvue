<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Link to logged-in user (nullable)
            $table->string('name')->nullable(); // Allow customers to change their name
            $table->string('email')->nullable(); // Allow customers to change their email
            $table->string('phone')->nullable(); // Allow customers to provide their phone number
            $table->string('address')->nullable(); // Allow customers to provide their address
            $table->string('payment_method')->nullable(); // Allow customers to specify payment method
            $table->decimal('total', 10, 2); // Order total price
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending'); // Order status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
