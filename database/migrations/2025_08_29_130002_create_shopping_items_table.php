<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingItemsTable extends Migration
{
    /**
     * Alışveriş ürünleri tablosunu oluşturur.
     */
    public function up()
    {
        Schema::create('shopping_items', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ürün adı
            $table->integer('quantity')->default(1); // Miktar
            $table->string('unit')->nullable(); // Birim
            $table->decimal('estimated_price', 10, 2)->nullable(); // Tahmini fiyat
            $table->boolean('is_purchased')->default(false); // Alındı mı?
            $table->text('notes')->nullable(); // Notlar
            $table->foreignId('shopping_list_id')->constrained()->onDelete('cascade'); // Liste ilişkisi
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null'); // Kategori ilişkisi
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shopping_items');
    }
}
