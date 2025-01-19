<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVariantCombinationsTable extends Migration
{
    public function up()
    {
        Schema::create('variant_combinations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->json('combination_values');
            $table->decimal('price', 8, 2)->nullable();
            $table->string('image')->nullable(); // To store the image path
            $table->timestamps();

            // Foreign key to product table
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('variant_combinations');
    }
}
