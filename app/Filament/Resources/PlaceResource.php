<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlaceResource\Pages;
use App\Filament\Resources\PlaceResource\RelationManagers;
use App\Models\Place;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlaceResource extends Resource
{
    protected static ?string $modelLabel = '地點';
    protected static ?string $pluralModelLabel = '地點列表';

    protected static ?string $model = Place::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('resource')
                    ->label('資源')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('unique_id')
                    ->label('唯一識別碼')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('formatted_address')
                    ->label('格式化地址')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->label('名稱')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('types')
                    ->label('類型')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('rating')
                    ->label('評分')
                    ->numeric()
                    ->step(0.1)
                    ->default(0),
                Forms\Components\TextInput::make('user_rating_count')
                    ->label('用戶評分數量')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('google_maps_uri')
                    ->label('Google 地圖 URI')
                    ->url()
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('名稱')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('types')
                    ->label('類型')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPlaces::route('/'),
            // 'create' => Pages\CreatePlace::route('/create'),
            'edit' => Pages\EditPlace::route('/{record}/edit'),
        ];
    }
}
