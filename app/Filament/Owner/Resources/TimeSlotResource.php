<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\TimeSlotResource\Pages;
use App\Models\Field;
use App\Models\TimeSlot;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TimeSlotResource extends Resource
{
    protected static ?string $model = TimeSlot::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Time Slots';

    protected static ?string $modelLabel = 'Time Slot';

    protected static ?string $pluralModelLabel = 'Time Slots';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('field_id')
                    ->label('Field')
                    ->options(fn (): array => Field::query()
                        ->where('owner_id', auth()->id())
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->required()
                    ->native(false),
                Select::make('day_of_week')
                    ->options([
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                        'Sunday' => 'Sunday',
                    ])
                    ->required()
                    ->native(false),
                TextInput::make('start_time')
                    ->type('time')
                    ->required(),
                TextInput::make('end_time')
                    ->type('time')
                    ->required(),
                Toggle::make('is_available_base')
                    ->label('Available by default')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isFieldOwner() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isFieldOwner() ?? false;
    }

    public static function canView(Model $record): bool
    {
        return $record instanceof TimeSlot && $record->field?->owner_id === auth()->id();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canView($record);
    }

    public static function canDelete(Model $record): bool
    {
        return static::canView($record);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('field', fn (Builder $query) => $query->where('owner_id', auth()->id()))
            ->with('field');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('day_of_week')
            ->columns([
                Tables\Columns\TextColumn::make('field.name')
                    ->label('Field')
                    ->searchable(),
                Tables\Columns\TextColumn::make('day_of_week')
                    ->label('Day')
                    ->badge(),
                Tables\Columns\TextColumn::make('slot_range')
                    ->label('Time Range')
                    ->state(fn (TimeSlot $record): string => substr($record->start_time, 0, 5) . ' - ' . substr($record->end_time, 0, 5)),
                Tables\Columns\BadgeColumn::make('is_available_base')
                    ->label('Status')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Available' : 'Disabled')
                    ->colors([
                        'success' => true,
                        'gray' => false,
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y, H:i')
                    ->toggleable(),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn (TimeSlot $record): string => static::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimeSlots::route('/'),
            'create' => Pages\CreateTimeSlot::route('/create'),
            'edit' => Pages\EditTimeSlot::route('/{record}/edit'),
        ];
    }
}
