<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DistrictResource\Pages;
use App\Filament\Resources\DistrictResource\RelationManagers;
use App\Models\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Cache;

class DistrictResource extends Resource
{
    protected static ?string $modelLabel = '區域';
    protected static ?string $pluralModelLabel = '區域列表';

    protected static ?string $model = District::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('區域名稱')->required(),
                Forms\Components\ToggleButtons::make('processed')->label('處理狀態')->boolean()->inline()->default(false)->required(),
                Forms\Components\TextInput::make('lat_min')->numeric()->step(0.0001)->required(),
                Forms\Components\TextInput::make('lat_max')->numeric()->step(0.0001)->required()->gt('lat_min'),
                Forms\Components\TextInput::make('lng_min')->numeric()->step(0.0001)->required(),
                Forms\Components\TextInput::make('lng_max')->numeric()->step(0.0001)->required()->gt('lng_min'),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->label('區域')->sortable()->searchable(),
                Tables\Columns\IconColumn::make('processed')->label('處理狀態')->boolean()->sortable(),
                Tables\Columns\TextColumn::make('progress_bar')
                    ->label('進度')
                    ->formatStateUsing(function (District $record) {
                        $progress = Cache::get($record->id . '_nearby_progress', 0);
                        $total = $record->grids_count ?: 1;
                        $percent = 100; //min(100, round(($progress / $total) * 100));
                        $bar = '<div style="background:#e5e7eb;border-radius:4px;width:100%;height:18px;position:relative;">
                            <div style="background:#3b82f6;width:' . $percent . '%;height:100%;border-radius:4px;"></div>
                            <div style="position:absolute;top:0;left:0;width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:12px;color:#111;">' . $percent . '%</div>
                        </div>';
                        return $bar;
                    })
                    ->html(),

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
            'index' => Pages\ListDistricts::route('/'),
            'create' => Pages\CreateDistrict::route('/create'),
            'edit' => Pages\EditDistrict::route('/{record}/edit'),
        ];
    }
}
