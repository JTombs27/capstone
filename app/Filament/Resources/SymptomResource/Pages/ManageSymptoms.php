<?php

namespace App\Filament\Resources\SymptomResource\Pages;

use App\Filament\Resources\SymptomResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSymptoms extends ManageRecords
{
    protected static string $resource = SymptomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
