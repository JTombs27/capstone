<?php

namespace App\Filament\Resources\HelplineResource\Pages;

use Filament\Actions;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\HelplineResource;
use Filament\Actions\Action;
use Filament\Support\Enums\ActionColor;
use Filament\Notifications\Notification;

class CreateHelpline extends CreateRecord
{
    protected static string $resource = HelplineResource::class;
    //public collection $symptoms;
    protected function getCreateFormAction(): Action
    {
        return
            Action::make('create')
            ->label('💾 Save') // Label for the default create
            ->submit('create');
    }
    protected function getCreatedNotificationMessage(): ?string
    {
        return "Case Report Successfully Added!";
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return   Action::make('createAnother')
            ->label('📝 Save & Add New') // Custom label here
            ->action('createAnother')
            ->extraAttributes([
                'class' => 'bg-green-600 hover:bg-green-700 text-white', // Customize as needed
            ]);
    }
    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
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



    protected function handleRecordCreation(array $data): Model
    {
        $disease = static::getModel()::create($data); // Create disease record

        // Ensure that symptoms are stored properly
        if (!empty($this->symptoms)) {
            $disease->helplineSymptoms()->createMany($this->symptoms);
        }

        return $disease;
    }


    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data); // Update disease record

        // Sync related disease symptoms (delete old and insert new ones)
        if (!empty($this->symptoms)) {
            $record->helplineSymptoms()->delete(); // Remove old symptoms
            $record->helplineSymptoms()->createMany($this->symptoms);
        }

        return $record;
    }
}
