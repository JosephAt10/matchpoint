<?php

namespace App\Filament\Owner\Resources\TimeSlotResource\Pages;

use App\Filament\Owner\Resources\TimeSlotResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTimeSlot extends CreateRecord
{
    protected static string $resource = TimeSlotResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
