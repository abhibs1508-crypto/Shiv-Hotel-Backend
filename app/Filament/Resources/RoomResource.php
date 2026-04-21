<?php

namespace App\Filament\Resources;

use App\Models\Room;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use App\Filament\Resources\RoomResource\Pages;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel Management';

    protected static ?int $navigationSort = 2;

    /* =========================
        FORM
    ========================== */

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([

            TextInput::make('room_number')
                ->required()
                ->unique(ignoreRecord: true),

            Select::make('type')
                ->options([
                    'standard' => 'Standard',
                    'deluxe' => 'Deluxe',
                    'suite' => 'Suite',
                    'presidential' => 'Presidential',
                ])
                ->required(),

            TextInput::make('price')
                ->numeric()
                ->required()
                ->prefix('₹'),

            Textarea::make('description')
                ->rows(3)
                ->columnSpanFull(),

            FileUpload::make('image')
                ->image()
                ->directory('rooms')
                ->imagePreviewHeight('150'),

            Select::make('status')
                ->options([
                    'available' => 'Available',
                    'booked' => 'Booked',
                    'maintenance' => 'Maintenance',
                ])
                ->default('available')
                ->required(),
        ]);
    }

    /* =========================
        TABLE
    ========================== */

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('image'),

                TextColumn::make('room_number')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('type'),

                TextColumn::make('price')
                    ->money('INR')
                    ->sortable(),

                BadgeColumn::make('status'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])

            // ✅ ONLY row actions here (v5 way)
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ]);
    }

    /* =========================
        PAGES
    ========================== */

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}