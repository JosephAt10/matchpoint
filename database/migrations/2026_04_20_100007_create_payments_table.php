<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                ->nullable()
                ->constrained('bookings')
                ->nullOnDelete();
            $table->foreignId('match_participant_id')
                ->nullable()
                ->constrained('match_participants')
                ->nullOnDelete();
            $table->foreignId('payer_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('proof', 255)->nullable();
            $table->enum('type', ['BookingDP', 'MatchFee']);
            $table->enum('status', ['Pending', 'Verified', 'Rejected'])->default('Pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
