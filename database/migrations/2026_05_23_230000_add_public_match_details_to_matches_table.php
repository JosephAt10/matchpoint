<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->string('title', 150)->nullable()->after('creator_id');
            $table->text('description')->nullable()->after('title');
            $table->unsignedSmallInteger('min_participants')->default(1)->after('max_participants');
            $table->string('gender', 30)->default('Open')->after('participant_fee');
            $table->string('skill_level', 50)->default('All Levels')->after('gender');
            $table->string('match_type', 50)->default('Friendly')->after('skill_level');
            $table->string('court_name', 100)->nullable()->after('match_type');
            $table->string('equipment', 150)->nullable()->after('court_name');
            $table->string('notes', 150)->nullable()->after('equipment');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'description',
                'min_participants',
                'gender',
                'skill_level',
                'match_type',
                'court_name',
                'equipment',
                'notes',
            ]);
        });
    }
};
