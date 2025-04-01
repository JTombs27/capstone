<?php

namespace App\Filament\Resources\RegisteredFarmResource\Pages;

use App\Filament\Resources\RegisteredFarmResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRegisteredFarm extends CreateRecord
{
    protected static string $resource = RegisteredFarmResource::class;

    public function getTitle(): string
    {
        return 'New Farm Registration'; // ✅ Custom page title
    }
}
