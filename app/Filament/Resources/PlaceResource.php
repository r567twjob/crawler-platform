<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlaceResource\Pages;
use App\Filament\Resources\PlaceResource\RelationManagers;
use App\Models\Place;
use App\Models\PlaceType;
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
                Forms\Components\Select::make('resource')
                    ->label('資源')
                    ->required()
                    ->disabled(fn($record) => $record?->id !== null)
                    ->options([
                        'google' => 'Google Maps',
                    ]),
                Forms\Components\TextInput::make('unique_id')
                    ->label('id')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn($record) => $record?->id !== null),
                Forms\Components\TextInput::make('formatted_address')
                    ->label('地址')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->label('名稱')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('types')
                    ->label('類型')
                    ->multiple()
                    ->options(function ($get) {
                        $resource = $get('resource');
                        return PlaceType::where('resource', $resource)->pluck('label', 'key')->toArray();
                    })
                    ->required()
                    ->reactive(),
                Forms\Components\TextInput::make('google_maps_uri')
                    ->label('網址')
                    ->url(),
                Forms\Components\TextInput::make('rating')
                    ->label('評分')
                    ->numeric()
                    ->step(0.1)
                    ->default(0),
                Forms\Components\TextInput::make('user_rating_count')
                    ->label('用戶評分數量')
                    ->numeric()
                    ->default(0),
            ])->columns(3);
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
                    ->searchable()
                    ->url(fn($record) => $record->google_maps_uri)
                    ->openUrlInNewTab(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('types')
                    ->label('類型')
                    ->multiple()
                    ->options(
                        \App\Models\PlaceType::pluck('label', 'key')->toArray()
                    )
                    ->query(function ($query, $data) {
                        if (!empty($data['values'])) {
                            $query->where(function ($q) use ($data) {
                                foreach ($data['values'] as $type) {
                                    $q->orWhereRaw("FIND_IN_SET(?, types)", [$type]);
                                }
                            });
                        }
                    }),
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
            'create' => Pages\CreatePlace::route('/create'),
            'edit' => Pages\EditPlace::route('/{record}/edit'),
        ];
    }
}
