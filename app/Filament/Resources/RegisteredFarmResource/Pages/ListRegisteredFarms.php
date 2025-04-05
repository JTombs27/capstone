<?php

namespace App\Filament\Resources\RegisteredFarmResource\Pages;

use App\Filament\Resources\RegisteredFarmResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegisteredFarms extends ListRecords
{
    protected static string $resource = RegisteredFarmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label("Register a Farm"),
        ];
    }
}
