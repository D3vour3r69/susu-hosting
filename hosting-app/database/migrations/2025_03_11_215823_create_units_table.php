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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('head_id')
                ->nullable()
                ->constrained('users')       // Связь с users
                ->onDelete('set null');      // При удалении пользователя → null
            $table->unsignedBigInteger('external_id')->nullable();
            $table->timestamps();

            $table->index('external_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
