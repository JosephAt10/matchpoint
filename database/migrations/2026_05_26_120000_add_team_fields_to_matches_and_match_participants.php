<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table): void {
            $table->string('team_a_name')->nullable()->after('description');
            $table->string('team_b_name')->nullable()->after('team_a_name');
            $table->string('team_a_logo', 255)->nullable()->after('team_b_name');
            $table->string('team_b_logo', 255)->nullable()->after('team_a_logo');
            $table->unsignedSmallInteger('max_per_team')->nullable()->after('team_b_logo');
        });

        Schema::table('match_participants', function (Blueprint $table): void {
            $table->enum('team', ['A', 'B'])->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('match_participants', function (Blueprint $table): void {
            $table->dropColumn('team');
        });

        Schema::table('matches', function (Blueprint $table): void {
            $table->dropColumn([
                'team_a_name',
                'team_b_name',
                'team_a_logo',
                'team_b_logo',
                'max_per_team',
            ]);
        });
    }
};
