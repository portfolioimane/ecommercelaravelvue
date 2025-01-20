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
      Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'Size', 'Color'
            $table->enum('type', ['color', 'label']); // Only 'color' or 'label' are allowed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};