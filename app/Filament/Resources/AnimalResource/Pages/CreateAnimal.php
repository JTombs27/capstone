<?php

namespace App\Filament\Resources\AnimalResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use App\Filament\Resources\AnimalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAnimal extends CreateRecord
{
    protected static string $resource = AnimalResource::class;

    public function getTitle(): string
    {
        return 'Add New Animal Type'; // ✅ Custom page title
    }
    protected function getCreateFormAction(): Action
    {
        return
            Action::make('create')
            ->label('💾 Save') // Label for the default create
            ->submit('create');
    }
    protected function getCreatedNotificationMessage(): ?string
    {
        return "New Animal Type Successfully Added!";
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
}
