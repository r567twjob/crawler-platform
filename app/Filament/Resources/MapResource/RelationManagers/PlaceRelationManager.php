<?php

namespace App\Filament\Resources\MapResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class PlaceRelationManager extends RelationManager
{
    protected static string $relationship = 'places';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ExportAction::make()
                    ->exports([
                        ExcelExport::make()
                            ->withColumns([
                                Column::make('unique_id')->heading('店家GoogleID'),
                                Column::make('name')->heading('店家名稱'),
                                Column::make('formatted_address')->heading('店家地址'),
                                Column::make('rating')->heading('店家評分'),
                                Column::make('user_rating_count')->heading('店家總評論數'),
                                Column::make('google_maps_uri')->heading('店家網址'),
                                Column::make('lat')->heading('緯度'),
                                Column::make('lng')->heading('經度'),
                            ])
                            ->fromTable()
                    ]),
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
