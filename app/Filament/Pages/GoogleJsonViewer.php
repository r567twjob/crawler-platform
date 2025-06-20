<?php

namespace App\Filament\Pages;

use App\Models\Grid;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Select;

class GoogleJsonViewer extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    public $name = 'google-json-viewer';
    public $message = "";
    public static ?string $title = "JSON 檔案檢視器 (Google)";
    public $availableFiles = [];
    public $selectedFile = null;
    public $selectedGrid = null;
    public $jsonData = [];

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.google-json-viewer';
    protected static ?string $navigationLabel = 'JSON (Google)';
    protected static ?string $navigationGroup = '工具';

    public function mount()
    {
        $this->availableFiles = Grid::all();

        // 預設載入第一個檔案
        $this->selectedGrid = Grid::first();
        $this->selectedFile = Grid::first()->id ?? null;
        $this->loadJsonData();
    }

    public function updated()
    {
        $this->selectedGrid = Grid::find($this->selectedFile);
        $this->loadJsonData();
    }

    public function loadJsonData()
    {
        $grid = $this->selectedGrid;
        $path = storage_path("app/places/{$grid->district->id}/{$grid->id}.json");
        if (File::exists($path)) {
            $json = json_decode(file_get_contents($path));
            if ($json === []) {
                $this->jsonData = [];
                $this->message = "沒有資料可供顯示。";
                return;
            } else {
                $this->jsonData = is_array($json->places) ? $json->places : [];
                $this->message = "沒有資料可供顯示。";
            }
        } else {
            $this->jsonData = [];
            $this->message = "尚未下載";
        }
    }

    public function downloadJson()
    {
        $grid = $this->selectedGrid;
        $path = storage_path("app/places/{$grid->district->id}/{$grid->id}.json");

        if (File::exists($path)) {
            return response()->download($path, "{$grid->district->name}-{$grid->id}.json");
        } else {
            Log::error("JSON file not found: {$path}");
            return back()->withErrors(['file' => 'JSON file not found.']);
        }
    }
}
