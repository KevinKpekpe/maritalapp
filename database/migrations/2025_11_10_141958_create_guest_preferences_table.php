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
        Schema::create('guest_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained('guests')->cascadeOnDelete();
            $table->foreignId('beverage_id')->constrained('beverages')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['guest_id', 'beverage_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_preferences');
    }
};
