<?php

namespace App\Filament\Pages;

use App\Models\Grid;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class JsonViewer extends Page
{
    public $name = 'json-viewer';
    public $availableFiles = [];
    public $selectedFile = null;
    public $jsonData = [];

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.json-viewer';
    protected static ?string $navigationLabel = 'JSON 檢視器';
    protected static ?string $navigationGroup = '工具';

    public function mount()
    {
        $this->availableFiles = Grid::all();

        // 預設載入第一個檔案
        $this->selectedFile = Grid::first()->id ?? null;
        $this->loadJsonData();
    }

    public function updated()
    {
        $this->loadJsonData();
    }

    public function loadJsonData()
    {
        $grid = Grid::find($this->selectedFile);
        // Log::info('Selected file updated', ['file' => $this->selectedFile]);
        $path = storage_path("app/places/{$grid->district->id}/{$grid->id}.json");
        if (File::exists($path)) {
            $json = json_decode(file_get_contents($path));

            $this->jsonData = is_array($json->places) ? $json->places : [];
            //dd($this->jsonData); // For debugging purposes, remove in production
        } else {
            $this->jsonData = [];
        }
    }
}
