<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku', 64)->unique();
            $table->string('size', 50)->nullable();
            $table->string('color', 50)->nullable();
            $table->string('color_hex', 7)->nullable();
            $table->decimal('price_override', 10, 2)->nullable();
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['product_id', 'is_active']);
            $table->index(['product_id', 'stock_quantity']);
            $table->index('size');
            $table->index('color');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
