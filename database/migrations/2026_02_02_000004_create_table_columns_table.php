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
        Schema::create('table_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crud_table_id')->constrained('crud_tables')->cascadeOnDelete();
            $table->string('column_name'); 
            $table->string('label'); 
            $table->string('description')->nullable();
            $table->enum('field_type', ['text', 'select', 'image']); 
            $table->json('options')->nullable(); 
            $table->string('css_class')->nullable(); 
            $table->string('icon')->nullable();
            $table->unsignedInteger('position')->default(0); 
            $table->boolean('is_required')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_columns');
    }
};
