<?php

namespace App\Filament\Resources\HelplineResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\HelplineResource;

class ListHelplines extends ListRecords
{
    protected static string $resource = HelplineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
