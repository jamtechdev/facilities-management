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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->nullable(); // chemicals, cloths, mops, machines, etc.
            $table->integer('quantity');
            $table->integer('min_stock_level')->default(0);
            $table->string('unit')->default('piece'); // piece, liter, kg, etc.
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->nullableMorphs('assigned_to'); // Polymorphic: can be assigned to Staff or Client (nullable for unassigned items)
            $table->enum('status', ['available', 'assigned', 'used', 'returned'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('category');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
