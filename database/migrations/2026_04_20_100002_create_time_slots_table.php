<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
            $table->enum('day_of_week', [
                'Monday', 'Tuesday', 'Wednesday', 'Thursday',
                'Friday', 'Saturday', 'Sunday',
            ]);
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_available_base')->default(true);
            $table->timestamps();

            $table->unique(['field_id', 'day_of_week', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_slots');
    }
};
