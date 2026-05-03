<?php

namespace App\Filament\Owner\Resources\FieldResource\Pages;

use App\Filament\Owner\Resources\FieldResource;
use Filament\Resources\Pages\EditRecord;

class EditField extends EditRecord
{
    protected static string $resource = FieldResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
