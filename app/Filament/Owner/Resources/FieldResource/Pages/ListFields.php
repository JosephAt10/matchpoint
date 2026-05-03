<?php

namespace App\Filament\Owner\Resources\FieldResource\Pages;

use App\Filament\Owner\Resources\FieldResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListFields extends ListRecords
{
    protected static string $resource = FieldResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add New Field'),
        ];
    }
}
