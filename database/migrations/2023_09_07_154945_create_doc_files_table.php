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
        Schema::create('doc_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('libro_id')->references('id')->on('libros')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
                  
            $table->string('doc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_files');
    }
};
