<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\FieldResource\Pages;
use App\Models\Field;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FieldResource extends Resource
{
    protected static ?string $model = Field::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'Fields';

    protected static ?string $modelLabel = 'Field';

    protected static ?string $pluralModelLabel = 'Fields';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(150),
                FileUpload::make('image_path')
                    ->label('Field Image')
                    ->image()
                    ->disk('public')
                    ->directory('fields')
                    ->visibility('public')
                    ->imageEditor()
                    ->columnSpanFull(),
                TextInput::make('location')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->options([
                        'Indoor' => 'Indoor',
                        'Outdoor' => 'Outdoor',
                    ])
                    ->required()
                    ->native(false),
                TextInput::make('sport_type')
                    ->label('Sport Type')
                    ->required()
                    ->maxLength(100),
                TextInput::make('price_per_slot')
                    ->label('Price Per Slot')
                    ->numeric()
                    ->required()
                    ->prefix('Rp'),
                Textarea::make('description')
                    ->rows(5)
                    ->columnSpanFull(),
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
        return $record instanceof Field && $record->owner_id === auth()->id();
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
            ->where('owner_id', auth()->id())
            ->withCount(['timeSlots', 'bookings']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Image')
                    ->disk('public')
                    ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sport_type')
                    ->label('Sport')
                    ->badge(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Outdoor' ? 'success' : 'info'),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('is_approved')
                    ->label('Approval')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Approved' : 'Pending')
                    ->colors([
                        'success' => true,
                        'warning' => false,
                    ]),
                Tables\Columns\TextColumn::make('time_slots_count')
                    ->label('Time Slots'),
                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Bookings'),
                Tables\Columns\TextColumn::make('price_per_slot')
                    ->label('Price')
                    ->formatStateUsing(fn ($state): string => 'Rp ' . number_format((float) $state, 0, ',', '.')),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn (Field $record): string => static::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFields::route('/'),
            'create' => Pages\CreateField::route('/create'),
            'edit' => Pages\EditField::route('/{record}/edit'),
        ];
    }
}
