<?php

namespace App\Filament\Resources\DistrictResource\Pages;

use App\Filament\Resources\DistrictResource;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\ActionSize;
use Illuminate\Support\Facades\Http;

class EditDistrict extends EditRecord
{
    protected static string $resource = DistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Actions\Action::make("google_map")
                    ->label("使用 Google Map(網格做法)")
                    ->action(function () {
                        $token = csrf_token();
                        $response = Http::withHeaders(['X-CSRF-TOKEN', $token])->post(route('google_map_crawler'), ["district" => $this->record->id]);
                        if ($response->failed()) {
                            Notification::make()->title("Google Map 爬蟲啟動失敗")->body($response->json("message"))->danger()->send();
                            return;
                        } else {
                            Notification::make()->title("Google Map 爬蟲已啟動")->success()->send();
                            redirect("/admin/districts");
                        }
                    })
                    ->requiresConfirmation()
                    ->hidden(fn() => $this->record->processed)
                    ->disabled(fn() => $this->record->processed),
            ])->label('爬蟲工具')
                ->icon("heroicon-o-globe-alt")
                ->color("primary")
                ->button(),

            Actions\DeleteAction::make()->requiresConfirmation(),
        ];
    }
}
