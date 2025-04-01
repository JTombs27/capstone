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
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\CheckboxList;
use App\Filament\Resources\HelplineResource;
use Filament\Forms\Components\Actions\Action;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Filament\Forms\Concerns\InteractsWithForms;

#[Title('Home Page - DDO-ADS-TRACE')]
class HomePage extends Component implements HasForms
{
    use LivewireAlert;
    use InteractsWithForms;

    public $municipality_id;
    public $symptomsAdded       = [];
    public $symptomsApplication = [];
    public $agree               = false;
    public $applicationModel    = [
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
        "death_count"           => 0,
        "latitude"              => null,
        "longitude"             => null,
        "other_info"            => "",
        "status"                => "Application",
        "diseasesymptoms"       => [],

        "farm_type"             => "",
        "start_date"            => "",
        "middle_name"           => "",
        "suffix"                => "",
        "owner_firstname"       => "",
        "owner_lastname"        => "",
        "owner_middlename"      => "",
        "owner_suffix"          => "",
        "owner_contactnumber"   => "",
    ];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        Section::make(new HtmlString('<b>Contributor Information</b>'))
                            ->schema([
                                Section::make(new HtmlString('<small><b>Ikaw nga nag report</b> </small>'))
                                    ->schema([
                                        TextInput::make('first_name')
                                            ->label("Pangalan")
                                            ->inlineLabel()
                                            ->maxLength(255)
                                            ->columnSpan(12),
                                        TextInput::make('last_name')
                                            ->label("Apilyedo")
                                            ->inlineLabel()
                                            ->maxLength(255)
                                            ->columnSpan(12),
                                        TextInput::make('middle_name')
                                            ->label("Middle Name")
                                            ->inlineLabel()
                                            ->maxLength(255)
                                            ->columnSpan(12),
                                        TextInput::make('suffix')
                                            ->label("Suffix Name")
                                            ->inlineLabel()
                                            ->maxLength(255)
                                            ->columnSpan(12),
                                        TextInput::make('contact_number')
                                            ->mask('09999999999') // Mask format for PH mobile numbers
                                            ->regex('/^(\+63|0)9\d{9}$/') // Validates PH numbers
                                            ->inlineLabel()
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(12),
                                    ])
                                    ->extraAlpineAttributes(['class' => 'no-border my-content', 'style' => 'padding:0px;']),
                                Section::make(new HtmlString('<small><b>Tag-iya sa gina report nga hayop.</b> </small>'))
                                    ->headerActions([
                                        Action::make('same_as_reporter')
                                            ->label('Same as Reporter')
                                            ->icon(fn(callable $get) => $get('same_as_reporter') ? 'heroicon-s-clipboard-document-check' : 'heroicon-o-document-duplicate')
                                            ->action(function (callable $set, callable $get) {
                                                $state = !$get('same_as_reporter');
                                                $set('same_as_reporter', $state);

                                                if ($state) {
                                                    $set('owner_firstname', $get('first_name'));
                                                    $set('owner_lastname', $get('last_name'));
                                                    $set('owner_middlename', $get('middle_name'));
                                                    $set('owner_suffix', $get('suffix'));
                                                    $set('owner_contactnumber', $get('contact_number'));
                                                } else {
                                                    $set('owner_firstname', '');
                                                    $set('owner_lastname', '');
                                                    $set('owner_middlename', '');
                                                    $set('owner_suffix', '');
                                                    $set('owner_contactnumber', '');
                                                }
                                            }),
                                    ])
                                    ->schema([
                                        TextInput::make('owner_firstname')
                                            ->label("Pangalan")
                                            ->inlineLabel()
                                            ->maxLength(255)
                                            ->columnSpan(12)
                                            ->disabled(fn(callable $get) => $get('same_as_reporter'))
                                            ->reactive(),
                                        TextInput::make('owner_lastname')
                                            ->label("Apilyedo")
                                            ->inlineLabel()
                                            ->maxLength(255)
                                            ->columnSpan(12)
                                            ->disabled(fn(callable $get) => $get('same_as_reporter'))
                                            ->reactive(),
                                        TextInput::make('owner_middlename')
                                            ->label("Middle Name")
                                            ->inlineLabel()
                                            ->maxLength(255)
                                            ->columnSpan(12)
                                            ->disabled(fn(callable $get) => $get('same_as_reporter'))
                                            ->reactive(),
                                        TextInput::make('owner_suffix')
                                            ->label("Suffix Name")
                                            ->inlineLabel()
                                            ->maxLength(255)
                                            ->columnSpan(12)
                                            ->disabled(fn(callable $get) => $get('same_as_reporter'))
                                            ->reactive(),
                                        TextInput::make('owner_contactnumber')
                                            ->label("Contact Number")
                                            ->mask('09999999999') // Mask format for PH mobile numbers
                                            ->regex('/^(\+63|0)9\d{9}$/') // Validates PH numbers
                                            ->inlineLabel()
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(12)
                                            ->disabled(fn(callable $get) => $get('same_as_reporter'))
                                            ->reactive(),
                                    ])
                                    ->extraAlpineAttributes(['class' => 'no-border my-content', 'style' => 'padding:0px;']),


                            ])
                            ->extraAlpineAttributes(['class' => 'no-border my-level2-content', 'style' => 'padding:0px;'])
                            ->columns(12)
                            ->columnSpan(5),
                        Section::make(new HtmlString('<b>Location</b> <small>(Marka sa mapa kung asa ang maong lugar nahibaluan ang maong sakit sa hayop)</small>'))

                            ->schema([
                                Select::make('query_municipality')
                                    ->label("Municipality")
                                    ->inlineLabel()
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
                                    ->inlineLabel()
                                    ->required()
                                    ->reactive()
                                    ->searchable()
                                    ->options(fn($get) =>  Barangay::where('municipality_id', $get('query_municipality'))->get()->pluck('barangay_name', 'id')->toArray())
                                    ->columnSpan(12),
                                TextInput::make('query_address')
                                    ->label("Street | Landmarks")
                                    ->inlineLabel()
                                    ->maxLength(255)
                                    ->columnSpan(12),
                                Map::make('location')
                                    ->label('')
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
                                        'min-height: 420px'
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
                                    // ->detectRetina()
                                    // // ->showMyLocationButton()
                                    // ->geoMan()
                                    // ->geoManEditable(false)
                                    // ->geoManPosition('topleft')
                                    // ->drawCircleMarker()
                                    // ->rotateMode()
                                    // ->drawPolygon()
                                    // ->drawPolyline()
                                    // ->drawCircle()
                                    // ->dragMode()
                                    // ->cutPolygon()
                                    // ->editPolygon()
                                    // ->deleteLayer()
                                    ->setColor('#3388ff')
                                    ->setFilledColor('#cad9ec')
                            ])
                            ->columnSpan(7)
                            ->extraAlpineAttributes(['class' => 'no-border my-content'])
                    ])
                    ->extraAlpineAttributes(['class' => 'no-border'])
                    ->columns(12)
                    ->columnSpan(12),
                Section::make(new HtmlString('<b>Animal Information</b> (Ditalyadong impormasyon mahitungod sa kahimtang sa hayop nga gina report)'))
                    ->schema([
                        Section::make()
                            ->schema([
                                Select::make('animal_id')
                                    ->label(new HtmlString("Unsa nga hayop"))
                                    ->options(Animal::where("animal_name", "!=", "All")->pluck('animal_name', 'id'))
                                    ->searchable()
                                    ->native(false)
                                    ->reactive() // Trigger updates when this field changes
                                    ->afterStateUpdated(fn(callable $set) => $set('diseasesymptoms', []))
                                    ->required()
                                    ->inlineLabel()
                                    ->columnspan(12),
                                DatePicker::make('start_date')
                                    ->label(new HtmlString("Kanus'a Nag sugod"))
                                    ->required()
                                    ->inlineLabel()
                                    ->columnSpan(12),
                                TextInput::make('affected_count')
                                    ->label(new HtmlString("Pila Apiktado"))
                                    ->required()
                                    ->inlineLabel()
                                    ->numeric()
                                    ->mask("9999")
                                    ->maxLength(10)
                                    ->columnSpan(12),
                                TextInput::make('death_count')
                                    ->label(new HtmlString("Pila Patay"))
                                    ->required()
                                    ->inlineLabel()
                                    ->numeric()
                                    ->mask("9999")
                                    ->maxLength(10)
                                    ->columnSpan(12),
                                Textarea::make('other_info')
                                    ->label(new HtmlString("Dugang Impormasyon"))
                                    ->rows(3)
                                    ->inlineLabel()
                                    ->columnspan(12),
                            ])
                            ->extraAlpineAttributes(['class' => 'no-border my-content'])
                            ->columns(12)
                            ->columnSpan(5),
                        Section::make()
                            ->schema([

                                FileUpload::make('image_path')
                                    ->label("Sample Images")
                                    ->multiple()
                                    ->maxParallelUploads(1)
                                    ->visible(false)
                                    ->columnspan(12),
                                Section::make(new HtmlString('<small><b>Pili usa ug hayop.<br/>Mga ginapakitang symptomas nga anaa makita sa maong hayop.</b></small> <small>E click ang box.</small>'))
                                    ->schema([
                                        CheckboxList::make("diseasesymptoms")
                                            ->label('')
                                            ->options(fn($get) =>  Symptom::where('animal_id', $get('animal_id'))->orderBy('symptom_descr')->get()->pluck('symptom_descr', 'id'))
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
                                    ->extraAlpineAttributes(['class' => 'no-border third-level-section'])
                            ])
                            ->extraAlpineAttributes(['class' => 'no-border'])
                            ->columns(12)
                            ->columnSpan(7),
                    ])
                    ->extraAlpineAttributes(['class' => 'no-border no-padding-sections-header mt-0 gap-1'])
                    ->columns(12)
                    ->columnSpan(12),
            ])
            ->statePath('applicationModel');
    }

    public function render()
    {
        return view('livewire.home-page');
    }
    public function test()
    {
        $this->dispatch('open-modal', id: 'privacy');
    }
    public function submitApplication()
    {
        if (!$this->agree) {
            $this->alert('error', 'You must agree to the Data Privacy Policy before submitting.', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true
            ]);
        } else {
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
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->symptoms = array_map(function ($symptom) {
            return ['symptom_id' => $symptom];
        }, $data['diseasesymptoms'] ?? []);

        return Arr::except($data, ['diseasesymptoms', 'same_as_reporter']);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->symptoms = array_map(function ($symptom) {
            return ['symptom_id' => $symptom];
        }, $data['diseasesymptoms'] ?? []);

        return Arr::except($data, ['diseasesymptoms', 'same_as_reporter']);
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
        $this->agree    = false;
    }
}
