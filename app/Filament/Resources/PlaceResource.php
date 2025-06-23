<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlaceResource\Pages;
use App\Filament\Resources\PlaceResource\RelationManagers;
use App\Models\Place;
use App\Models\PlaceType;
use DeepCopy\Filter\Filter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

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
                Tables\Columns\TextColumn::make('lat')
                    ->label('緯度')
                    ->sortable(),
                Tables\Columns\TextColumn::make('lng')
                    ->label('經度')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('評分')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_rating_count')
                    ->label('評論數')
                    ->sortable()
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

                Tables\Filters\Filter::make('location_radius')
                    ->label('經緯度範圍')
                    ->form([
                        Forms\Components\TextInput::make('center_lat')->label('中心緯度')->numeric(),
                        Forms\Components\TextInput::make('center_lng')->label('中心經度')->numeric(),
                        Forms\Components\TextInput::make('radius')->label('半徑(公尺)')->numeric(),
                    ])
                    ->query(function ($query, $data) {
                        if (
                            isset($data['center_lat'], $data['center_lng'], $data['radius']) &&
                            $data['center_lat'] !== null &&
                            $data['center_lng'] !== null &&
                            $data['radius'] !== null
                        ) {
                            $lat = (float)$data['center_lat'];
                            $lng = (float)$data['center_lng'];
                            $radius = (float)$data['radius'];

                            // 地球半徑 (公尺)
                            $earthRadius = 637100;

                            // 緯度範圍
                            $latDelta = rad2deg($radius / $earthRadius);

                            // 經度範圍 (需考慮緯度)
                            $lngDelta = rad2deg($radius / $earthRadius / cos(deg2rad($lat)));

                            $minLat = $lat - $latDelta;
                            $maxLat = $lat + $latDelta;
                            $minLng = $lng - $lngDelta;
                            $maxLng = $lng + $lngDelta;

                            $query->whereBetween('lat', [$minLat, $maxLat])
                                ->whereBetween('lng', [$minLng, $maxLng]);
                        }
                    }),

                Tables\Filters\Filter::make('rating')
                    ->label('評分')
                    ->form([
                        Forms\Components\TextInput::make('min_rating')->label('最小評分')->numeric(),
                    ])
                    ->query(function ($query, $data) {
                        if ($data['min_rating'] !== null) {
                            $query->where('rating', '>=', $data['min_rating']);
                        }
                    }),

                Tables\Filters\Filter::make('user_rating_count')
                    ->label('評論數')
                    ->form([
                        Forms\Components\TextInput::make('min_count')->label('最小評論數')->numeric(),
                    ])
                    ->query(function ($query, $data) {
                        if ($data['min_count'] !== null) {
                            $query->where('user_rating_count', '>=', $data['min_count']);
                        }
                    }),



            ], layout: FiltersLayout::AboveContent) // 將篩選器放在表格上方
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),

                ExportBulkAction::make()->exports([
                    ExcelExport::make()->withColumns([
                        Column::make('unique_id')->heading('店家GoogleID'),
                        Column::make('name')->heading('店家名稱'),
                        Column::make('formatted_address')->heading('店家地址'),
                        Column::make('rating')->heading('店家評分'),
                        Column::make('user_rating_count')->heading('店家總評論數'),
                        Column::make('google_maps_uri')->heading('店家網址'),
                        Column::make('lat')->heading('緯度'),
                        Column::make('lng')->heading('經度'),
                    ]),
                ]),

                Tables\Actions\BulkAction::make('add_to_map_place')
                    ->label('加入到地圖')
                    ->form([
                        Forms\Components\Select::make('map_id')
                            ->label('選擇地圖')
                            ->options(\App\Models\Map::pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(function ($records, $data) {
                        foreach ($records as $record) {
                            DB::table('map_place')->updateOrInsert(
                                ['map_id' => $data['map_id'], 'place_id' => $record->id],
                                ['created_at' => now(), 'updated_at' => now()]
                            );
                        }
                    })
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion()
                    ->icon('heroicon-o-plus'),
            ])
            ->paginated([10, 20, 30]) // 加入分頁選項
            ->defaultPaginationPageOption(10);
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
