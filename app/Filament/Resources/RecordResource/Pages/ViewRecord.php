<?php

namespace App\Filament\Resources\RecordResource\Pages;

use App\Filament\Resources\RecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord as ViewRecordBase;

class ViewRecord extends ViewRecordBase
{
    protected static string $resource = RecordResource::class;

    public function getViewData(): array
    {
        return [
            'resource' => $this->record->resource,
            'id' => $this->record->id,
        ];
    }
}
