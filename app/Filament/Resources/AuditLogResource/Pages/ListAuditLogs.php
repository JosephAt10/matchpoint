<?php

namespace App\Filament\Resources\AuditLogResource\Pages;

use App\Filament\Resources\AuditLogResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
