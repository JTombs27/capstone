<?php

namespace App\Livewire;

use App\Models\Animal;
use App\Models\Symptom;
use Filament\Forms\Set;
use Livewire\Component;
use App\Models\Barangay;
use App\Models\Helpline;
use Filament\Forms\Form;
use Illuminate\Support\Arr;
use App\Models\Municipality;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use App\Models\HelplineSymptom;
use Dotswan\MapPicker\Fields\Map;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\CheckboxList;
use App\Filament\Resources\HelplineResource;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Filament\Forms\Concerns\InteractsWithForms;

#[Title('Help - DDO-ADS-TRACE')]

class AnimalHelp extends Component implements HasForms
{
    use LivewireAlert;
    use InteractsWithForms;
    //#[Url]
    public $municipality_id;
    public $symptomsAdded = [];
    public $symptomsApplication = [];
    public $agree = false;
    public $applicationModel = [
        "user_id"               => null,
        "first_name"            => "",
        "last_name"             => "",
        "animal_id"             => "",
        "disease_id"            => null,
        "contact_number"        => "",
        "query_municipality"    => "",
        "query_barangay"        => "",
        "image_path"            => "",
        "query_address"         => "",
        "affected_count"        => 0,
        "death_count"           => null,
        "latitude"              => null,
        "longitude"             => null,
        "other_info"            => "",
        "status"                => "Application",
        "diseasesymptoms"       => []
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        Section::make('Contributor Information')
                            ->schema([
                                TextInput::make('first_name')
                                    ->maxLength(255)
                                    ->columnSpan(6),
                                TextInput::make('last_name')
                                    ->maxLength(255)
                                    ->columnSpan(6),
                                TextInput::make('contact_number')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(12),
                                Select::make('query_municipality')
                                    ->label("Municipality")
                                    ->options(Municipality::all()->pluck('municipality_name', 'id'))
                                    ->searchable()
                                    ->native(false)
                                    ->reactive() // Make the field reactive
                                    ->afterStateUpdated(function (callable $set, $state, $livewire): void {
                                        if ($state) {
                                            $municipality = Municipality::find($state);
                                            // $set('latitude', $municipality->lat); // Set latitude
                                            // $set('longitude', $municipality->lng); // Set longitude
                                            $set('location', [
                                                'lat' => $municipality->lat,
                                                'lng' => $municipality->lng,
                                                'zoom' => 20
                                            ]);
                                            $livewire->dispatch('refreshMap');
                                        }
                                    })
                                    ->required()
                                    ->columnSpan(12),
                                Select::make('query_barangay')
                                    ->label("Barangay")
                                    ->required()
                                    ->reactive()
                                    ->searchable()
                                    ->options(fn($get) =>  Barangay::where('municipality_id', $get('query_municipality'))->get()->pluck('barangay_name', 'id')->toArray())
                                    ->columnSpan(12),
                                TextInput::make('query_address')
                                    ->label("Street | Landmarks")
                                    ->maxLength(255)
                                    ->columnSpan(12),
                            ])
                            ->extraAlpineAttributes(['class' => 'no-border'])
                            ->columns(12)
                            ->columnSpan(6),
                        Section::make()
                            ->schema([
                                Map::make('location')
                                    ->label('Location')
                                    ->columnSpan(12)
                                    ->defaultLocation(7.6038, 125.9632)
                                    ->afterStateUpdated(function (Set $set, ?array $state) {
                                        if ($state) {
                                            $set('latitude',  $state['lat']);
                                            $set('longitude', $state['lng']);
                                        }
                                    })
                                    ->afterStateHydrated(function ($state, $record, Set $set): void {

                                        $set('location', ['lat' => $record?->latitude, 'lng' => $record?->longitude]);
                                    })
                                    ->extraStyles([
                                        'min-height: 500px'
                                    ])
                                    //->liveLocation(true, true, 5000)
                                    ->showMarker(true)
                                    ->clickable(true) //click to move marker
                                    ->drawMarker(true)
                                    ->draggable(true)
                                    ->markerColor("#22c55eff")
                                    ->markerHtml('<div class="custom-marker"><img src="/images/farmMarker.png"></div>')
                                    ->markerIconUrl('/images/farmMarker.png')
                                    ->markerIconSize([50, 50])
                                    ->markerIconClassName('my-marker-class')
                                    ->markerIconAnchor([16, 32])
                                    ->showFullscreenControl()
                                    ->showZoomControl()
                                    ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
                                    //->tilesUrl("http://{s}.google.com/vt?lyrs=m&x={x}&y={y}&z={z}")
                                    ->zoom(10)
                                    ->detectRetina()
                                    // ->showMyLocationButton()
                                    ->geoMan()
                                    ->geoManEditable(false)
                                    ->geoManPosition('topleft')
                                    ->drawCircleMarker()
                                    ->rotateMode()
                                    ->drawPolygon()
                                    ->drawPolyline()
                                    ->drawCircle()
                                    ->dragMode()
                                    ->cutPolygon()
                                    ->editPolygon()
                                    ->deleteLayer()
                                    ->setColor('#3388ff')
                                    ->setFilledColor('#cad9ec')
                            ])
                            ->columnSpan(6)
                            ->extraAlpineAttributes(['class' => 'no-border'])
                    ])
                    ->extraAlpineAttributes(['class' => 'no-border'])
                    ->columns(12)
                    ->columnSpan(12),
                Section::make('Animal Information')
                    ->schema([
                        Section::make()
                            ->schema([
                                Select::make('animal_id')
                                    ->label("Select Animal")
                                    ->options(Animal::where("animal_name", "!=", "All")->pluck('animal_name', 'id'))
                                    ->searchable()
                                    ->native(false)
                                    ->reactive() // Trigger updates when this field changes
                                    ->afterStateUpdated(fn(callable $set) => $set('diseasesymptoms', []))
                                    ->required()
                                    ->columnspan(4),
                                TextInput::make('affected_count')
                                    ->required()
                                    ->numeric()
                                    ->maxLength(255)
                                    ->columnSpan(4),
                                TextInput::make('death_count')
                                    ->required()
                                    ->numeric()
                                    ->maxLength(255)
                                    ->columnSpan(4),
                                Section::make('List Of Symptoms')
                                    ->schema([
                                        CheckboxList::make("diseasesymptoms")
                                            ->label('')
                                            ->options(fn($get) =>  Symptom::where('animal_id', $get('animal_id'))->get()->pluck('symptom_descr', 'id'))
                                            ->gridDirection('row')
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record != null) {
                                                    $component->state(DB::table('helpline_symptoms')
                                                        ->join('symptoms', 'symptoms.id', '=', 'helpline_symptoms.symptom_id')
                                                        ->where('helpline_symptoms.helpline_id', $record->id)
                                                        ->pluck('symptoms.id')
                                                        ->toArray());
                                                }
                                            })
                                            ->reactive() // Make this field reactive
                                            ->columns(2)
                                            ->bulkToggleable()
                                    ])
                                    ->columnSpan(12)
                            ])
                            ->extraAlpineAttributes(['class' => 'no-border'])
                            ->columns(12)
                            ->columnSpan(8),
                        Section::make()
                            ->schema([
                                Textarea::make('other_info')
                                    ->rows(3)
                                    ->columnspan(12),
                                FileUpload::make('image_path')
                                    ->label("Sample Images")
                                    ->multiple()
                                    ->maxParallelUploads(1)
                                    ->columnspan(12),
                            ])
                            ->extraAlpineAttributes(['class' => 'no-border'])
                            ->columns(12)
                            ->columnSpan(4),
                    ])
                    ->extraAlpineAttributes(['class' => 'no-border'])
                    ->extraAlpineAttributes(['class' => 'no-padding-sections-header'])
                    ->columns(12)
                    ->columnSpan(12),
            ])
            ->statePath('applicationModel');
    }

    public function render()
    {
        $municipalities = Municipality::all();
        $barangays      = Barangay::all();
        $barangays  = $barangays->where("municipality_id", $this->applicationModel["query_municipality"]);
        $animals    = Animal::all();
        $animals    = $animals->where("animal_name", "!=", "All");
        $symptoms   = Symptom::all();
        $symptoms = $symptoms->where("animal_id", $this->applicationModel["animal_id"]);
        return view('livewire.animal-help', [
            "municipality" => $municipalities,
            "barangays" => $barangays,
            "animals" => $animals,
            "symptoms" => $symptoms
        ]);
    }

    // public function submitApplication()
    // {
    //     $animal_id = $this->applicationModel["animal_id"];
    //     $this->symptomsApplication = [];

    //     // Create the parent record and get its ID
    //     $helpline = Helpline::create($this->applicationModel);

    //     // Prepare the array of child records
    //     foreach ($this->symptomsAdded as $value) {
    //         $this->symptomsApplication[] = [
    //             "symptom_id" => $value,
    //             "helpline_id" => $helpline->id // Use the ID from the created parent
    //         ];
    //     }

    //     // Insert multiple child records
    //     HelplineSymptom::insert($this->symptomsApplication);
    //     $this->alert('success', 'Application successfully submitted!', [
    //         'position' => 'top-end',
    //         'timer' => 3000,
    //         'toast' => true
    //     ]);

    //     $this->applicationModel = [
    //         "user_id"               => null,
    //         "first_name"            => "",
    //         "last_name"             => "",
    //         "animal_id"             => "",
    //         "disease_id"            => null,
    //         "contact_number"        => "",
    //         "query_municipality"    => "",
    //         "query_barangay"        => "",
    //         "image_path"            => "",
    //         "query_address"         => "",
    //         "affected_count"        => 0,
    //         "death_count"           => null,
    //         "latitude"              => null,
    //         "longitude"             => null,
    //         "other_info"            => "",
    //         "status"                => "Application"
    //     ];
    // }
    // public function resetSymptoms()
    // {
    //     $this->symptomsAdded = [];
    // }

    public $symptoms = [];

    public function submitApplication()
    {
        if (!$this->agree) {
            session()->flash('error', 'You must agree to the Data Privacy Policy before submitting.');
            return;
        }
        // Process form data before saving
        $data = $this->mutateFormDataBeforeSave($this->applicationModel);

        // Create helpline record
        $helpline = $this->handleRecordCreation($data);

        // Success message
        $this->alert('success', 'Application successfully submitted!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true
        ]);

        // Reset form fields after submission
        $this->resetForm();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->symptoms = array_map(function ($symptom) {
            return ['symptom_id' => $symptom];
        }, $data['diseasesymptoms'] ?? []);

        return Arr::except($data, ['diseasesymptoms']);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->symptoms = array_map(function ($symptom) {
            return ['symptom_id' => $symptom];
        }, $data['diseasesymptoms'] ?? []);

        return Arr::except($data, ['diseasesymptoms']);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $helpline = Helpline::create($data);

        // Insert symptoms if available
        if (!empty($this->symptoms)) {
            $helpline->helplineSymptoms()->createMany($this->symptoms);
        }

        return $helpline;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        // Sync symptoms (remove old, insert new)
        if (!empty($this->symptoms)) {
            $record->helplineSymptoms()->delete();
            $record->helplineSymptoms()->createMany($this->symptoms);
        }

        return $record;
    }

    private function resetForm()
    {
        $this->applicationModel = [
            "user_id"               => null,
            "first_name"            => "",
            "last_name"             => "",
            "animal_id"             => "",
            "disease_id"            => null,
            "contact_number"        => "",
            "query_municipality"    => "",
            "query_barangay"        => "",
            "image_path"            => "",
            "query_address"         => "",
            "affected_count"        => 0,
            "death_count"           => null,
            "latitude"              => null,
            "longitude"             => null,
            "other_info"            => "",
            "status"                => "Application",
            "diseasesymptoms"       => [] // Reset symptoms
        ];
        $this->symptoms = [];
    }
}
