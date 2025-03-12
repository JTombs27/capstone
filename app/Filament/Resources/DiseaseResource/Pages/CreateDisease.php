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

    //public collection $symptoms;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->symptoms = array_map(function ($symptom) {
            return ['symptom_id' => $symptom]; // Convert to associative array
        }, $data['diseasesymptoms'] ?? []);

        return Arr::except($data, ['diseasesymptoms']);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->symptoms = array_map(function ($symptom) {
            return ['symptom_id' => $symptom]; // Convert to associative array
        }, $data['diseasesymptoms'] ?? []);

        // Remove `diseasesymptoms` from the data to prevent errors
        unset($data['diseasesymptoms']);

        return Arr::except($data, ['diseasesymptoms']);
    }



    protected function handleRecordCreation(array $data): Model
    {
        $disease = static::getModel()::create($data); // Create disease record

        // Ensure that symptoms are stored properly
        if (!empty($this->symptoms)) {
            $disease->diseaseSymptoms()->createMany($this->symptoms);
        }

        return $disease;
    }


    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data); // Update disease record

        // Sync related disease symptoms (delete old and insert new ones)
        if (!empty($this->symptoms)) {
            $record->diseaseSymptoms()->delete(); // Remove old symptoms
            $record->diseaseSymptoms()->createMany($this->symptoms);
        }

        return $record;
    }
}
