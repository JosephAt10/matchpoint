<?php

namespace Database\Seeders;

use App\Models\Field;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FieldImageSeeder extends Seeder
{
    public function run(): void
    {
        $sportImageMap = [
            'Futsal' => 'futsal-court.png',
            'Badminton' => 'badminton-court.png',
            'Football' => 'football-stadium.jpg',
            'Basketball' => 'basketball-court.jpg',
            'Tennis' => 'tennis-court.png',
            'Volleyball' => 'volleyball-court.png',
        ];

        Field::query()->get()->each(function (Field $field) use ($sportImageMap): void {
            if (filled($field->image_path) && Storage::disk('public')->exists($field->image_path)) {
                return;
            }

            $sourceFilename = $sportImageMap[$field->sport_type] ?? 'football-stadium.jpg';
            $sourcePath = public_path('landing/' . $sourceFilename);

            if (! is_file($sourcePath)) {
                $this->command?->warn("Missing source image for {$field->name}: {$sourceFilename}");

                return;
            }

            $extension = pathinfo($sourceFilename, PATHINFO_EXTENSION);
            $targetPath = 'fields/' . Str::slug($field->name) . '.' . $extension;

            Storage::disk('public')->put($targetPath, file_get_contents($sourcePath));

            $field->forceFill([
                'image_path' => $targetPath,
            ])->save();

            $this->command?->info("Image synced for {$field->name}");
        });
    }
}
