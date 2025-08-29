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
        Schema::create('suppliers', function (Blueprint $table) {
            
            $table->id();
            $table->string('vkn', 50); // Vergi Kimlik No 
            $table->string('mersis_no', 50)->nullable();
            $table->string('ticaret_sicil_no', 50)->nullable();
            $table->string('name'); // Firma adı
            $table->string('tax_office')->nullable(); // Vergi dairesi
            $table->text('address')->nullable(); 
            $table->string('building_number', 50)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('contact_first_name', 100)->nullable();
            $table->string('contact_family_name', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
