<?php

namespace App\Filament\Resources;

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
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Filament\Resources\HelplineResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\HelplineResource\RelationManagers;

class HelplineResource extends Resource
{

    protected static ?string $model = Helpline::class;

    protected static ?string $navigationGroup   = "SERVICES";
    protected static ?string $navigationIcon    = 'heroicon-o-lifebuoy';
    protected static ?string $navigationLabel   = "Animal Helpline";
    protected static ?string $label             = "Animal Helpline";
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
                                            // ->zoom(10)
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
                                        Section::make('List Of Symptoms')
                                            ->schema([
                                                Forms\Components\CheckboxList::make("diseasesymptoms")
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

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('animal.animal_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('disease.disease_description')
                    ->label("Disease")
                    ->getStateUsing(fn($record) => $record->disease->disease_description ?? 'Unverified Disease')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->getStateUsing(
                        fn($record) => ($record->latitude && $record->longitude) ? $record->status . '- Location Set' : $record->status . ' -Location Not Set'
                    )
                    ->searchable(),
                // Tables\Columns\TextColumn::make('location')
                //     ->label('Location')
                //     ->getStateUsing(
                //         fn($record) => ($record->latitude && $record->longitude) ? 'Location Set' : 'Location Not Set'
                //     ),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make()
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

                        ])
                        ->slideOver(),
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
                        })
                        ->slideOver(),
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
                        ->form([
                            // Dynamically load diseases based on the animal_id
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
                        ])

                        ->modalWidth("md")
                        ->action(function (array $data, $record) {
                            // Update the record with the selected disease
                            $record->disease_id = $data["help_disease"];
                            $record->save();
                        }),
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
