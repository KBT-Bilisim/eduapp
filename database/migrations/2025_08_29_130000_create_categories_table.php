<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Kategoriler tablosunu oluşturur.
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // Birincil anahtar
            $table->string('name'); // Kategori adı
            $table->string('icon')->nullable(); // Font Awesome ikonu
            $table->string('color')->nullable(); // Renk kodu
            $table->timestamps(); // created_at ve updated_at
            $table->softDeletes();
        });
    }

    /**
     * Tabloyu geri alır.
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
