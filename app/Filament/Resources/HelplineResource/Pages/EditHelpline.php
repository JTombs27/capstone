<?php

namespace App\Filament\Resources\HelplineResource\Pages;

use App\Filament\Resources\HelplineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHelpline extends EditRecord
{
    protected static string $resource = HelplineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
