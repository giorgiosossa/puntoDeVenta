<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord:true)
                    ->maxLength(255),

                Forms\Components\TextInput::make('sku')
                    ->required()
                    ->unique(ignoreRecord:true)
                    ->maxLength(100)
                    ->helperText('Código único para identificar el producto.'),

                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->prefix('$')
                    ->step(0.01),

                Forms\Components\TextInput::make('stock')
                    ->numeric()
                    ->required()
                    ->helperText('Cantidad disponible en inventario.')
                    ->afterStateUpdated(function ($state, $set, $record) {
                        if ($state < 5) {
                            Notification::make()
                                ->title('Stock Bajo')
                                ->body("El producto '{$record->name}' tiene menos de 5 unidades disponibles.")
                                ->danger()
                                ->send();
                        }
                    }),

                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('products')
                    ->columnSpanFull()
                    ->helperText('Sube una imagen del producto (JPG, PNG, GIF).'),

                Forms\Components\Select::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->helperText('Selecciona una o más categorías para el producto.'),

                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Activo',
                        'inactive' => 'Inactivo',
                    ])
                    ->default('active')
                    ->required()
                    ->helperText('Define si el producto está disponible o no.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\ImageColumn::make('image')
                ->size(50),

            Tables\Columns\TextColumn::make('name')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('sku')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('price')
                ->money('USD')
                ->sortable(),

            Tables\Columns\TextColumn::make('stock')
                ->sortable()
                ->color(fn ($record) => 
                    $record->stock < 5 ? 'danger' : ($record->stock < 10 ? 'warning' : 'success')
                ),

            Tables\Columns\TextColumn::make('categories.name')
                ->badge()
                ->sortable(),

            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->sortable(),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                ->options([
                    'active' => 'Activo',
                    'inactive' => 'Inactivo',
                ]),
            Tables\Filters\Filter::make('stock bajo')
                ->query(fn ($query) => $query->where('stock', '<', 5)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
