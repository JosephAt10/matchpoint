<?php

namespace App\Filament\Owner\Resources\TimeSlotResource\Pages;

use App\Filament\Owner\Resources\TimeSlotResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListTimeSlots extends ListRecords
{
    protected static string $resource = TimeSlotResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add Time Slot'),
        ];
    }
}
