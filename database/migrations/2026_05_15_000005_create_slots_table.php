<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->boolean('is_booked')->default(false);
            $table->timestamps();

            $table->index(['service_id', 'start_at']);
            $table->index('is_booked');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
