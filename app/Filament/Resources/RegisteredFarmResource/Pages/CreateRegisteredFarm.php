<?php

namespace App\Filament\Resources\RegisteredFarmResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Support\Enums\ActionColor;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\RegisteredFarmResource;

class CreateRegisteredFarm extends CreateRecord
{
    protected static string $resource = RegisteredFarmResource::class;
    protected static bool $canCreateAnother = true;


    protected function getCreateFormAction(): Action
    {
        return
            Action::make('create')
            ->label('💾 Save') // Label for the default create
            ->submit('create');
    }
    protected function getCreatedNotificationMessage(): ?string
    {
        return "Farm data Successfully Added!";
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return   Action::make('createAnother')
            ->label('📝 Save & Add New') // Custom label here
            ->action('createAnother')
            ->extraAttributes([
                'class' => 'bg-green-600 hover:bg-green-700 text-white', // Customize as needed
            ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data["farm_status"] = "For Survey";
        return $data;
    }
    public function getTitle(): string
    {
        return 'New Farm Registration'; // ✅ Custom page title
    }
}
