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
        Schema::create('section_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('section_name');
            $table->text('original_section_text')->nullable();
            $table->json('notes')->nullable(); // Store notes as JSON
            $table->json('advice')->nullable(); // Store advice as JSON
            $table->text('enhanced_section_text')->nullable();
            $table->unsignedTinyInteger('score')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_feedback');
    }
};
