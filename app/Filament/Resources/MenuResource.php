<?php

namespace App\Filament\Resources;

use App\Models\Menu;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use App\Filament\Resources\MenuResource\Pages;
use Filament\Actions;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel Management';

    protected static ?int $navigationSort = 1;

    /* =========================
        FORM
    ========================== */

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([

            TextInput::make('name')
                ->required()
                ->maxLength(255),

            Select::make('category_id')
                ->relationship('category', 'name')
                ->searchable()
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
                ->directory('menus')
                ->imagePreviewHeight('150'),

            Toggle::make('is_signature')
                ->label('Signature Item'),

            Toggle::make('is_available')
                ->label('Available')
                ->default(true),
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

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->badge(),

                TextColumn::make('price')
                    ->money('INR'),

                IconColumn::make('is_signature')
                    ->boolean(),

                IconColumn::make('is_available')
                    ->boolean(),
            ])

            ->recordActions([
                Actions\EditAction::make(),
            ])

            ->toolbarActions([
                Actions\CreateAction::make(),   // ✅ THIS WAS MISSING
                Actions\DeleteBulkAction::make(),
            ]);
    }

    /* =========================
        PAGES
    ========================== */

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}