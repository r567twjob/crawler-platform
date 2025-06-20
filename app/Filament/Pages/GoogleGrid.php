<?php

namespace App\Filament\Pages;

use App\Models\Grid;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Select;

class GoogleGrid extends Page
{
    public $name = 'csv-import';
    public $message = "";
    public static ?string $title = "CSV Importer";


    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.google-grid';
    protected static ?string $navigationLabel = '單一資料建立';
    protected static ?string $navigationGroup = '工具';

    public function mount() {}

    public function updated() {}
}
