<?php

namespace App\Filament\Resources\DiseaseResource\Pages;

use Filament\Actions;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\DiseaseResource;
use BezhanSalleh\FilamentShield\Support\Utils;

class CreateDisease extends CreateRecord
{
    protected static string $resource = DiseaseResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return Arr::except($data, ['diseasesymptoms']);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return Arr::except($data, ['diseasesymptoms']);
    }


    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data = Arr::except($data, ['diseasesymptoms']);
        $record->update($data);

        return $record;
    }
}
