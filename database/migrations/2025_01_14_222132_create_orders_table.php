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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relacja do tabeli users
            $table->decimal('total_price', 10, 2); // Całkowita cena zamówienia
            $table->string('status')->default('pending'); // Status zamówienia
            $table->boolean('is_paid')->default(false); // Status płatności
            $table->string('shipment_status')->default('przyjęta'); // Status przesyłki
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
