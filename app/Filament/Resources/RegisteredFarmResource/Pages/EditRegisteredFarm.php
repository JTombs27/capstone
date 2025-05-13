<?php

namespace App\Filament\Resources\RegisteredFarmResource\Pages;

use App\Filament\Resources\RegisteredFarmResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegisteredFarm extends EditRecord
{
    protected static string $resource = RegisteredFarmResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationMessage(): ?string
    {
        return "Farm data Successfully Updated!";
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
