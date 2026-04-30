<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('pendingOwners')
                ->label('Pending Field Owners')
                ->icon('heroicon-o-user-plus')
                ->color('warning')
                ->url(UserResource::getUrl('index', [
                    'tableFilters[role][value]' => 'FieldOwner',
                    'tableFilters[status][value]' => 'PendingApproval',
                ])),
        ];
    }
}
