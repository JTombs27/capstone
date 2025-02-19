<?php

namespace App\Filament\Resources\MunicipalityResource\Pages;

use App\Filament\Resources\MunicipalityResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMunicipalities extends ManageRecords
{
    protected static string $resource = MunicipalityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
