<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('pendingReview')
                ->label('Pending Review')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->url(BookingResource::getUrl('index', [
                    'tableFilters[status][value]' => 'Pending',
                    'tableFilters[payment_status][value]' => 'Pending',
                ])),
        ];
    }
}
