<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('barbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barbershop_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->string('specialization')->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->unsignedInteger('experience_years')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barbers');
    }
};
