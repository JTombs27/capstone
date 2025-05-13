<?php

namespace App\Filament\Resources\DiseaseResource\Pages;

use Filament\Actions;
use Illuminate\Support\Arr;
use Filament\Actions\Action;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\DiseaseResource;
use BezhanSalleh\FilamentShield\Support\Utils;

class CreateDisease extends CreateRecord
{
    protected static string $resource = DiseaseResource::class;
    protected static ?string $label = 'Add New Disease';


    protected static ?string $breadcrumb = "Add New Disease";
    protected function getCreateFormAction(): Action
    {
        return
            Action::make('create')
            ->label('💾 Save') // Label for the default create
            ->submit('create');
    }
    protected function getCreatedNotificationMessage(): ?string
    {
        return "New disease Successfully Added!";
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

    public function getTitle(): string
    {
        return 'Register New Disease'; // ✅ Custom page title
    }

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
