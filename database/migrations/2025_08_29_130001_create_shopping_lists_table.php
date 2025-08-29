<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingListsTable extends Migration
{
    /**
     * Alışveriş listeleri tablosunu oluşturur.
     */
    public function up()
    {
        Schema::create('shopping_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Liste adı
            $table->text('description')->nullable(); // Açıklama
            $table->boolean('is_completed')->default(false); // Tamamlandı mı?
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Kullanıcı ilişkisi
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shopping_lists');
    }
}
