<?php

namespace App\Filament\Resources;

use App\Models\Category;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\CategoryResource\Pages;
use Filament\Actions;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel Management';

    protected static ?int $navigationSort = 4;

    /* =========================
        FORM
    ========================== */

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([

            // Category Name
            TextInput::make('name')
                ->required()
                ->unique(ignoreRecord: true)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state, callable $set) {
                    $set('slug', Str::slug($state));
                }),

            // Slug (Auto-filled but editable)
            TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true),

            // Description
            Textarea::make('description')
                ->rows(3),
        ]);
    }

    /* =========================
        TABLE
    ========================== */

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->limit(40),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])

            ->recordActions([
                Actions\EditAction::make(),
            ])

            ->toolbarActions([
                Actions\CreateAction::make(),
                Actions\DeleteBulkAction::make(),
            ]);
    }

    /* =========================
        PAGES
    ========================== */

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}