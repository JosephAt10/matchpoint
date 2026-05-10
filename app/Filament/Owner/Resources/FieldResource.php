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

    public static function getNavigationLabel(): string
    {
        return __('Fields');
    }

    public static function getModelLabel(): string
    {
        return __('Field');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Fields');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->validationAttribute(__('Name'))
                    ->validationMessages([
                        'required' => __('validation.required', ['attribute' => __('Name')]),
                        'max.string' => __('validation.max.string', ['attribute' => __('Name'), 'max' => 150]),
                    ])
                    ->required()
                    ->maxLength(150),
                FileUpload::make('image_path')
                    ->label(__('Field Image'))
                    ->validationAttribute(__('Field Image'))
                    ->validationMessages([
                        'image' => __('validation.image', ['attribute' => __('Field Image')]),
                        'uploaded' => __('validation.uploaded', ['attribute' => __('Field Image')]),
                    ])
                    ->image()
                    ->disk('public')
                    ->directory('fields')
                    ->visibility('public')
                    ->imageEditor()
                    ->columnSpanFull(),
                TextInput::make('location')
                    ->label(__('Location'))
                    ->validationAttribute(__('Location'))
                    ->validationMessages([
                        'required' => __('validation.required', ['attribute' => __('Location')]),
                        'max.string' => __('validation.max.string', ['attribute' => __('Location'), 'max' => 255]),
                    ])
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label(__('Field Type'))
                    ->validationAttribute(__('Field Type'))
                    ->validationMessages([
                        'required' => __('validation.required', ['attribute' => __('Field Type')]),
                    ])
                    ->options([
                        'Indoor' => __('Indoor'),
                        'Outdoor' => __('Outdoor'),
                    ])
                    ->required()
                    ->native(false),
                TextInput::make('sport_type')
                    ->label(__('Sport Type'))
                    ->validationAttribute(__('Sport Type'))
                    ->validationMessages([
                        'required' => __('validation.required', ['attribute' => __('Sport Type')]),
                        'max.string' => __('validation.max.string', ['attribute' => __('Sport Type'), 'max' => 100]),
                    ])
                    ->required()
                    ->maxLength(100),
                TextInput::make('price_per_slot')
                    ->label(__('Price Per Slot'))
                    ->validationAttribute(__('Price Per Slot'))
                    ->validationMessages([
                        'required' => __('validation.required', ['attribute' => __('Price Per Slot')]),
                        'numeric' => __('validation.numeric', ['attribute' => __('Price Per Slot')]),
                    ])
                    ->numeric()
                    ->required()
                    ->prefix('Rp'),
                Textarea::make('description')
                    ->label(__('About this venue'))
                    ->validationAttribute(__('About this venue'))
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
                    ->label(__('Image'))
                    ->getStateUsing(fn (Field $record): ?string => $record->image_url ? url($record->image_url) : null)
                    ->checkFileExistence(false)
                    ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sport_type')
                    ->label(__('Sport'))
                    ->badge(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('Field Type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __($state))
                    ->color(fn (string $state): string => $state === 'Outdoor' ? 'success' : 'info'),
                Tables\Columns\TextColumn::make('location')
                    ->label(__('Location'))
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('approval_status')
                    ->label(__('Approval'))
                    ->formatStateUsing(fn (string $state): string => __($state))
                    ->colors([
                        'success' => 'Approved',
                        'warning' => 'Pending',
                        'danger' => 'Rejected',
                    ]),
                Tables\Columns\TextColumn::make('time_slots_count')
                    ->label(__('Time Slots')),
                Tables\Columns\TextColumn::make('bookings_count')
                    ->label(__('Bookings')),
                Tables\Columns\TextColumn::make('price_per_slot')
                    ->label(__('Price'))
                    ->formatStateUsing(fn ($state): string => 'Rp ' . number_format((float) $state, 0, ',', '.')),
            ])
            ->actions([
                Action::make('edit')
                    ->label(__('Edit'))
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