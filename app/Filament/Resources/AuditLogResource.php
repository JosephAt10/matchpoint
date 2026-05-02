<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Audit Logs';

    protected static ?string $modelLabel = 'Audit Log';

    protected static ?string $pluralModelLabel = 'Audit Logs';

    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with('actor')
                ->latest('created_at'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('actor.name')
                    ->label('Actor')
                    ->placeholder('System')
                    ->searchable(),
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('entity_type')
                    ->label('Entity')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('entity_id')
                    ->label('Entity ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('metadata_summary')
                    ->label('Details')
                    ->state(function (AuditLog $record): string {
                        if (blank($record->metadata)) {
                            return '-';
                        }

                        return collect($record->metadata)
                            ->map(fn ($value, $key) => $key . ': ' . (is_array($value) ? json_encode($value) : (string) $value))
                            ->implode(' | ');
                    })
                    ->wrap()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('entity_type')
                    ->options(fn (): array => AuditLog::query()
                        ->select('entity_type')
                        ->distinct()
                        ->orderBy('entity_type')
                        ->pluck('entity_type', 'entity_type')
                        ->all()),
                SelectFilter::make('action')
                    ->options(fn (): array => AuditLog::query()
                        ->select('action')
                        ->distinct()
                        ->orderBy('action')
                        ->pluck('action', 'action')
                        ->all()),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }
}
