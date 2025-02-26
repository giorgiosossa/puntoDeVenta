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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Nombre único para evitar duplicados
            $table->string('sku')->unique(); // SKU único
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0.00); // Precio con dos decimales
            $table->integer('stock')->default(0); // Stock inicial en 0
            $table->string('image')->nullable(); // Imagen (URL o nombre de archivo)
            $table->enum('status', ['active', 'inactive'])->default('active'); // Estado del producto
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
