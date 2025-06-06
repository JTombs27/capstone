<?php

namespace App\Filament\Resources\HelplineResource\Pages;

use Filament\Actions;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\HelplineResource;

class EditHelpline extends EditRecord
{
    protected static string $resource = HelplineResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationMessage(): ?string
    {
        return "Case Report Successfully Updated!";
    }

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



    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data); // Update disease record
        if (!empty($this->symptoms)) {
            $record->helplineSymptoms()->delete(); // Remove old symptoms
            $record->helplineSymptoms()->createMany($this->symptoms);
        }

        return $record;
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
