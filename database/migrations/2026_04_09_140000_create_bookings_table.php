<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('barbershop_id')->constrained();
            $table->foreignId('barber_id')->constrained();
            $table->dateTime('scheduled_at');
            $table->string('status')->default('pending');
            $table->text('comment')->nullable();
            $table->boolean('reminder_enabled')->default(false);
            $table->decimal('total_price', 10, 2);
            $table->unsignedInteger('total_duration_minutes');
            $table->timestamps();

            $table->index(['barber_id', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
