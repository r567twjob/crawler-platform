<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecordResource\Pages;
use App\Filament\Resources\RecordResource\RelationManagers;
use App\Models\Record;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist as Infolist;

class RecordResource extends Resource
{
    protected static ?string $modelLabel = '紀錄';
    protected static ?string $pluralModelLabel = '紀錄列表';
    protected static ?string $model = Record::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('resource')
                    ->label("名稱")
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PlacesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecords::route('/'),
            'view'  => Pages\ViewRecord::route('/{record}'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('name'),
                Infolists\Components\TextEntry::make('email'),
                Infolists\Components\TextEntry::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
