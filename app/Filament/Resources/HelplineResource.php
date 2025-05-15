<?php

namespace App\Filament\Resources;

use DateTime;
use livewire;
use Filament\Forms;
use Filament\Tables;
use App\Models\Animal;
use App\Models\Disease;
use App\Models\Symptom;
use Filament\Forms\Set;
use App\Models\Barangay;
use App\Models\Helpline;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Municipality;
use App\Models\HelplineSymptom;
use Doctrine\DBAL\Schema\Schema;
use Filament\Resources\Resource;
use Dotswan\MapPicker\Fields\Map;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\View;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Livewire\Livewire as LivewireMount;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Filament\Resources\HelplineResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\HelplineResource\RelationManagers;


class HelplineResource extends Resource
{

    protected static ?string $model = Helpline::class;

    // protected static ?string $navigationGroup   = "SERVICES";
    protected static ?string $navigationIcon    = 'heroicon-o-lifebuoy';
    protected static ?string $navigationLabel   = "Animal Disease Cases";
    protected static ?string $label             = "Animal Disease Cases";
    public function getBreadcrumbs(): array
    {
        return [];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', "application")->count();
    }
    public static function getNavigationBadgeColor(): ?string
    {
        // return static::getModel()::count() > 10 ? 'warning' : 'primary';
        return "danger";
    }
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
                                            ->maxLength(255)
                                            ->columnSpan(12),
                                    ])
                                    ->extraAlpineAttributes(['class' => 'no-border my-content', 'style' => 'padding:0px;']),
                                Section::make(new HtmlString('<small><b>Tag-iya sa gina report nga hayop.</b> </small>'))
                                    // ->headerActions([
                                    //     Action::make('same_as_reporter')
                                    //         ->label('Same as Reporter')
                                    //         ->icon(fn(callable $get) => $get('same_as_reporter') ? 'heroicon-s-clipboard-document-check' : 'heroicon-o-document-duplicate')
                                    //         ->action(function (callable $set, callable $get) {
                                    //             $state = !$get('same_as_reporter');
                                    //             $set('same_as_reporter', $state);

                                    //             if ($state) {
                                    //                 $set('owner_firstname', $get('first_name'));
                                    //                 $set('owner_lastname', $get('last_name'));
                                    //                 $set('owner_middlename', $get('middle_name'));
                                    //                 $set('owner_suffix', $get('suffix'));
                                    //                 $set('owner_contactnumber', $get('contact_number'));
                                    //             } else {
                                    //                 $set('owner_firstname', '');
                                    //                 $set('owner_lastname', '');
                                    //                 $set('owner_middlename', '');
                                    //                 $set('owner_suffix', '');
                                    //                 $set('owner_contactnumber', '');
                                    //             }
                                    //         }),
                                    // ])
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
                                    ->zoom(10)
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
                                DateTimePicker::make('date_reported')
                                    ->label(new HtmlString("Reported date"))
                                    ->required()
                                    ->inlineLabel()
                                    ->columnSpan(12),
                                Textarea::make('other_info')
                                    ->label(new HtmlString("Dugang Impormasyon"))
                                    ->rows(3)
                                    ->inlineLabel()
                                    ->columnspan(12),
                                TextInput::make('affected_count')
                                    ->label(new HtmlString("Pila Apiktado"))
                                    ->required()
                                    //->inlineLabel()
                                    ->numeric()
                                    ->mask("9999")
                                    ->maxLength(10)
                                    ->columnSpan(3),
                                TextInput::make('death_count')
                                    ->label(new HtmlString("Pila Patay"))
                                    ->required()
                                    //->inlineLabel()
                                    ->numeric()
                                    ->mask("9999")
                                    ->maxLength(10)
                                    ->columnSpan(3),
                                TextInput::make('sample_count')
                                    ->label(new HtmlString("Sample Count"))
                                    ->required()
                                    //->inlineLabel()
                                    ->numeric()
                                    ->mask("9999")
                                    ->maxLength(10)
                                    ->columnSpan(3),
                                TextInput::make('positive_count')
                                    ->label(new HtmlString("Nag positibo"))
                                    ->required()
                                    //->inlineLabel()
                                    ->numeric()
                                    ->mask("9999")
                                    ->maxLength(10)
                                    ->columnSpan(3),

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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('municipal.municipality_name')
                    ->label("📍 Address Location")
                    ->extraCellAttributes(['class' => 'dictionary-cell', 'style' => 'width: 120px;'])
                    ->getStateUsing(fn($record) => "<small><b>" . $record->barangay->barangay_name . '</b></small>, <small><i>' . $record->municipal->municipality_name  . '</i></small>')
                    ->html()
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_number')
                    ->label(new HtmlString("📞 Contact#"))
                    ->extraCellAttributes(['class' => 'dictionary-cell', 'style' => 'width: 120px;'])
                    ->searchable(),
                Tables\Columns\TextColumn::make('animal.animal_name')
                    ->label(new HtmlString("Animal"))
                    ->extraCellAttributes(['class' => 'dictionary-cell'])
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('disease.disease_description')
                    ->extraCellAttributes(['class' => 'dictionary-cell'])
                    ->label("Disease")
                    ->getStateUsing(fn($record) => $record->disease->disease_description ?? 'Unverified Disease' . $record->other_info)
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('helplineSymptoms.Symptomx.symptom_descr')
                    ->extraCellAttributes(['class' => 'dictionary-cell'])
                    ->label("Reported Symptoms")
                    ->searchable()
                    ->expandableLimitedList()
                    ->bulleted()
                    ->wrap(),

                Tables\Columns\TextColumn::make('status')
                    ->extraCellAttributes(['class' => 'dictionary-cell'])
                    ->getStateUsing(
                        fn($record) =>
                        "<small> Active Status: " . $record->status
                            . "<br/>Disease Start Date: " . $record->start_date . ""
                            . "<br/>Reported Date: " . $record->date_reported . "</small>"

                    )
                    ->html()
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'DESC')
            ->defaultSort('date_reported', 'DESC')
            ->filters([
                // SelectFilter::make('status')
                //     ->options([
                //         'Monitored' => 'Monitored',
                //         'reviewing' => 'Reviewing',
                //         'published' => 'Published',
                //     ])
                //     ->default('Monitored')
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make()
                        ->modalWidth("5xl")
                        ->closeModalByClickingAway(false)
                        ->form([
                            Section::make('')
                                ->schema([
                                    Section::make('')
                                        ->schema([
                                            Section::make('Contributor Information')
                                                ->schema([
                                                    Forms\Components\TextInput::make('first_name')
                                                        ->required()
                                                        ->maxLength(255)
                                                        ->columnSpan(6),
                                                    Forms\Components\TextInput::make('last_name')
                                                        ->required()
                                                        ->maxLength(255)
                                                        ->columnSpan(6),
                                                    Forms\Components\TextInput::make('contact_number')
                                                        ->required()
                                                        ->maxLength(255)
                                                        ->columnSpan(12),
                                                    Forms\Components\Select::make('query_municipality')
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
                                                    Forms\Components\Select::make('query_barangay')
                                                        ->label("Barangay")
                                                        ->required()
                                                        ->searchable()
                                                        ->options(fn($get) =>  Barangay::where('municipality_id', $get('query_municipality'))->get()->pluck('barangay_name', 'id')->toArray())
                                                        ->columnSpan(12),
                                                    Forms\Components\TextInput::make('query_address')
                                                        ->label("Street | Landmarks")
                                                        ->required()
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
                                                    Forms\Components\Select::make('animal_id')
                                                        ->label("Select Animal")
                                                        ->options(Animal::where("animal_name", "!=", "All")->pluck('animal_name', 'id'))
                                                        ->searchable()
                                                        ->native(false)
                                                        ->reactive() // Trigger updates when this field changes
                                                        ->afterStateUpdated(fn(callable $set) => $set('diseasesymptoms', []))
                                                        ->required()
                                                        ->columnspan(4),
                                                    Forms\Components\TextInput::make('affected_count')
                                                        ->required()
                                                        ->numeric()
                                                        ->maxLength(255)
                                                        ->columnSpan(4),
                                                    Forms\Components\TextInput::make('death_count')
                                                        ->required()
                                                        ->numeric()
                                                        ->maxLength(255)
                                                        ->columnSpan(4),
                                                    Section::make('Reported Symptoms')
                                                        ->schema([
                                                            Forms\Components\CheckboxList::make('diseasesymptoms')
                                                                ->label("")
                                                                ->options(function ($record) {

                                                                    return DB::table('helpline_symptoms')
                                                                        ->join('symptoms', 'symptoms.id', '=', 'helpline_symptoms.symptom_id')
                                                                        ->where('helpline_symptoms.helpline_id', $record->id)
                                                                        ->pluck('symptoms.symptom_descr', 'symptoms.id')
                                                                        ->toArray();
                                                                })
                                                                ->afterStateHydrated(function ($component, $state, $record) {
                                                                    if (! filled($state)) {
                                                                        $component->state(DB::table('helpline_symptoms')
                                                                            ->join('symptoms', 'symptoms.id', '=', 'helpline_symptoms.symptom_id')
                                                                            ->where('helpline_symptoms.helpline_id', $record->id)
                                                                            ->pluck('symptoms.id')
                                                                            ->toArray());
                                                                    }
                                                                })
                                                                ->gridDirection('row')
                                                                ->reactive() // Ensure it updates when animal_id changes
                                                                ->columns(1)
                                                                ->bulkToggleable()
                                                        ])
                                                        ->columnSpan(12)
                                                ])
                                                ->extraAlpineAttributes(['class' => 'no-border'])
                                                ->columns(12)
                                                ->columnSpan(8),
                                            Section::make()
                                                ->schema([
                                                    Forms\Components\Textarea::make('other_info')
                                                        ->required()
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
                                        ->columns(12)
                                        ->columnSpan(12),
                                ])
                                ->columns(12)
                                ->extraAlpineAttributes(['class' => 'no-padding-sections-header']),

                        ]),
                    // ->slideOver(),
                    Tables\Actions\DeleteAction::make(),
                    Action::make("set_location")
                        ->icon("heroicon-s-map")
                        ->form(
                            [
                                Section::make("")
                                    ->schema([

                                        Map::make('location')
                                            ->label('Location')
                                            ->columnSpan(12)
                                            ->defaultLocation(latitude: 7.6038, longitude: 125.9632)
                                            ->afterStateUpdated(function (Set $set, ?array $state) {})
                                            ->afterStateHydrated(function ($state, $record, Set $set, $livewire): void {
                                                $set('location', ['lat' => $record?->latitude, 'lng' => $record?->longitude]);

                                                $livewire->dispatch('refreshMap');
                                            })
                                            ->extraStyles([
                                                'min-height: 400px',
                                                'z-index:1'
                                            ])
                                            //->liveLocation(true, true, 5000)
                                            ->showMarker(true)
                                            ->clickable(true) //click to move marker
                                            ->drawMarker(true)
                                            ->draggable(true)
                                            ->markerColor("#22c55eff")
                                            ->markerHtml('<div class="custom-marker"><img src="/images/farmMarker.png"></div>')
                                            ->markerIconUrl('/images/diseaseMarker.png')
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
                                    ->columns(12)

                            ]
                        )
                        ->modalWidth("xl")
                        ->modalAlignment(Alignment::Center)
                        ->action(function (array $data, $record) {

                            $record->latitude = $data["location"]["lat"];
                            $record->longitude = $data["location"]["lng"];
                            $record->save();
                        }),
                    Action::make('status')
                        ->label("Set Status")
                        ->icon("heroicon-s-signal")
                        ->form([
                            Select::make('status')
                                ->label("Select Status")
                                ->options([
                                    'Application' => 'Application',
                                    'For Survey' => 'For Survey',
                                    'Confirmed' => 'Confirmed',
                                    'Actioned' => 'Actioned',
                                    'Cleared' => 'Cleared',
                                    'Monitored' => 'Monitored',
                                    'Not Responsive' => 'Not Responsive'
                                ])
                        ])
                        ->modalWidth("md")
                        ->action(function (array $data, $record) {
                            $record->status = $data["status"];
                            $record->save();
                        }),
                    Action::make("set_disease")
                        ->label("Set Disease")
                        ->icon("heroicon-s-exclamation-triangle")
                        ->form(
                            function ($record) {
                                return [
                                    // Dynamically load diseases based on the animal_id
                                    Section::make()
                                        ->schema([
                                            Select::make('help_disease')
                                                ->label("Select Disease")
                                                ->options(function ($get, $record) {
                                                    // Get the animal_id from the current record
                                                    $animalId = $record->animal_id;

                                                    // Fetch diseases based on the animal_id
                                                    return Disease::where("animal_id", $animalId)
                                                        ->pluck('disease_description', 'id')
                                                        ->toArray();
                                                })
                                                ->columnSpanFull()
                                                ->default($record->diseas_id),
                                            TextInput::make("sample_count")
                                                ->label("Sample Count")
                                                ->default($record->sample_count)
                                                ->columnSpan(6),

                                            TextInput::make("positive_count")
                                                ->label("Positive Count")
                                                ->default($record->positive_count)
                                                ->columnSpan(6),
                                        ])
                                        ->columns(12)

                                ];
                            }
                        )
                        ->modalWidth("md")
                        ->modalContent(function ($record) {
                            $helplineSymptomIds = DB::table('helpline_symptoms')
                                ->where('helpline_id', $record->id)
                                ->pluck('symptom_id')
                                ->toArray();

                            if (empty($helplineSymptomIds)) {
                                // No symptoms found for this helpline, return empty result
                                $diseases = null;
                            } else {


                                // Quote the values properly for raw query
                                $quotedIds = implode(',', array_map('intval', $helplineSymptomIds));

                                $diseases = DB::table('diseases')
                                    ->join('disease_symptoms', 'diseases.id', '=', 'disease_symptoms.disease_id')
                                    ->select(
                                        'diseases.id',
                                        'diseases.disease_description',
                                        DB::raw("COUNT(CASE WHEN disease_symptoms.symptom_id IN ($quotedIds) THEN 1 END) as match_count"),
                                        DB::raw('COUNT(disease_symptoms.symptom_id) as total_symptoms')
                                    )
                                    ->groupBy('diseases.id', 'diseases.disease_description')
                                    ->get()
                                    ->map(function ($disease) {
                                        $percentage = $disease->total_symptoms > 0
                                            ? round(($disease->match_count / $disease->total_symptoms) * 100)
                                            : 0;
                                        return [
                                            'name' => $disease->disease_description,
                                            'percentage' => $percentage . '%',
                                        ];
                                    })
                                    ->sortByDesc('percentage')
                                    ->take(3)
                                    ->values();
                            }

                            return view('filament.modals.disease-bar-chart', ['details' => $diseases]);
                        })
                        ->action(function (array $data, $record) {
                            // Update the record with the selected disease
                            $record->disease_id = $data["help_disease"];
                            $record->sample_count = $data["sample_count"];
                            $record->positive_count = $data["positive_count"];
                            $record->save();
                        })
                        ->successNotificationTitle('Successfully Updated Disease'),
                    Action::make('unset_disease')
                        ->label("Unset Disease")
                        ->icon("heroicon-s-arrow-uturn-right")
                        ->action(function (array $data, $record) {
                            // Update the record with the selected disease
                            $record->disease_id = null;
                            $record->save();
                        })
                        ->requiresConfirmation()
                ])
                    ->icon("heroicon-s-cog-6-tooth")


            ]);
        // ->bulkActions([
        //     Tables\Actions\BulkActionGroup::make([
        //         Tables\Actions\DeleteBulkAction::make(),
        //     ]),
        // ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHelplines::route('/'),
            'create' => Pages\CreateHelpline::route('/create'),
            'edit' => Pages\EditHelpline::route('/{record}/edit'),
            //'view-map' => Pages\LeafletPage::route('/{record}/view-map')
        ];
    }
}
