<?php

namespace App\Filament\Owner\Resources\TimeSlotResource\Pages;

use App\Filament\Owner\Resources\TimeSlotResource;
use App\Models\Field;
use App\Models\TimeSlot;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateTimeSlot extends CreateRecord
{
    protected static string $resource = TimeSlotResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $field = Field::query()
            ->where('owner_id', auth()->id())
            ->findOrFail($data['field_id']);

        $days = array_values($data['days_of_week'] ?? []);
        $openingMinutes = $this->timeToMinutes($data['opening_time'] ?? null);
        $closingMinutes = $this->timeToMinutes($data['closing_time'] ?? null);

        if ($closingMinutes === 0) {
            $closingMinutes = 24 * 60;
        }

        if (blank($days)) {
            throw ValidationException::withMessages([
                'days_of_week' => 'Select at least one day.',
            ]);
        }

        if ($openingMinutes === null || $closingMinutes === null) {
            throw ValidationException::withMessages([
                'opening_time' => 'Opening and closing times are required.',
            ]);
        }

        if ($closingMinutes <= $openingMinutes) {
            throw ValidationException::withMessages([
                'closing_time' => 'Closing time must be after opening time. Use 00:00 for midnight.',
            ]);
        }

        if (($closingMinutes - $openingMinutes) < 60 || (($closingMinutes - $openingMinutes) % 60) !== 0) {
            throw ValidationException::withMessages([
                'closing_time' => 'The time range must produce full one-hour slots.',
            ]);
        }

        $firstSlot = DB::transaction(function () use ($field, $days, $openingMinutes, $closingMinutes, $data): ?TimeSlot {
            $firstCreatedSlot = null;

            foreach ($days as $day) {
                for ($startMinutes = $openingMinutes; $startMinutes < $closingMinutes; $startMinutes += 60) {
                    $slotData = [
                        'field_id' => $field->id,
                        'day_of_week' => $day,
                        'start_time' => $this->minutesToTime($startMinutes),
                        'end_time' => $this->minutesToTime($startMinutes + 60),
                        'is_available_base' => (bool) ($data['is_available_base'] ?? true),
                    ];

                    $slot = TimeSlot::query()->updateOrCreate(
                        [
                            'field_id' => $slotData['field_id'],
                            'day_of_week' => $slotData['day_of_week'],
                            'start_time' => $slotData['start_time'],
                        ],
                        [
                            'end_time' => $slotData['end_time'],
                            'is_available_base' => $slotData['is_available_base'],
                        ],
                    );

                    $firstCreatedSlot ??= $slot;
                }
            }

            return $firstCreatedSlot;
        });

        return $firstSlot ?? TimeSlot::query()->latest('id')->firstOrFail();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Time slots generated successfully';
    }

    private function timeToMinutes(?string $time): ?int
    {
        if (blank($time)) {
            return null;
        }

        [$hours, $minutes] = array_map('intval', explode(':', substr($time, 0, 5)));

        return ($hours * 60) + $minutes;
    }

    private function minutesToTime(int $minutes): string
    {
        $minutes %= (24 * 60);

        return sprintf('%02d:%02d:00', intdiv($minutes, 60), $minutes % 60);
    }
}
