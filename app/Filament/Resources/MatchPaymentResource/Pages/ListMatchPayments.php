<?php

namespace App\Filament\Resources\MatchPaymentResource\Pages;

use App\Filament\Resources\MatchPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListMatchPayments extends ListRecords
{
    protected static string $resource = MatchPaymentResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('pendingReview')
                ->label(__('Pending Match Payments'))
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->url(MatchPaymentResource::getUrl('index', [
                    'tableFilters[status][value]' => 'Pending',
                ])),
        ];
    }
}
