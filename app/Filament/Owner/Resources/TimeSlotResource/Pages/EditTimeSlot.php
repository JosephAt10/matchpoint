<?php

namespace App\Filament\Owner\Resources\TimeSlotResource\Pages;

use App\Filament\Owner\Resources\TimeSlotResource;
use Filament\Resources\Pages\EditRecord;

class EditTimeSlot extends EditRecord
{
    protected static string $resource = TimeSlotResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
