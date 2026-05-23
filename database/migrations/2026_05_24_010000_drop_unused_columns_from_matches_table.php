<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn([
                'min_participants',
                'gender',
                'match_type',
                'court_name',
                'equipment',
                'notes',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->unsignedSmallInteger('min_participants')->default(1)->after('max_participants');
            $table->string('gender', 30)->default('Open')->after('participant_fee');
            $table->string('match_type', 50)->default('Friendly')->after('skill_level');
            $table->string('court_name', 100)->nullable()->after('match_type');
            $table->string('equipment', 150)->nullable()->after('court_name');
            $table->string('notes', 150)->nullable()->after('equipment');
        });
    }
};
