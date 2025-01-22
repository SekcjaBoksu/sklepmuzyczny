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
            $table->string('title'); // Tytuł albumu
            $table->string('artist'); // Artysta lub zespół
            $table->date('release_date')->nullable(); // Data wydania
            $table->decimal('price', 10, 2); // Cena
            $table->integer('stock')->unsigned()->default(0); // Ilość sztuk z domyślną wartością
            $table->enum('format', ['CD', 'Vinyl', 'Special Edition']); // Format: CD, winyl, specjalne
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // Relacja do kategorii
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
