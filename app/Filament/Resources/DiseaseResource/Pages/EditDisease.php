<?php

namespace App\Filament\Resources\DiseaseResource\Pages;

use Filament\Actions;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\DiseaseResource;

class EditDisease extends EditRecord
{
    protected static string $resource = DiseaseResource::class;
    //public collection $symptoms;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure `diseasesymptoms` is an array of arrays

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

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
