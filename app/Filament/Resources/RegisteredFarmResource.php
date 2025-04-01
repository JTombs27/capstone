<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Animal;
use Filament\Forms\Set;
use App\Models\Barangay;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Municipality;
use App\Models\RegisteredFarm;
use Filament\Resources\Resource;
use Dotswan\MapPicker\Fields\Map;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RegisteredFarmResource\Pages;
use Filament\Infolists\Components\Section as ComponentsSection;
use App\Filament\Resources\RegisteredFarmResource\RelationManagers;

class RegisteredFarmResource extends Resource
{
    protected static ?string $model = RegisteredFarm::class;

    protected static ?string $navigationIcon = 'icon-animals';

    protected int $zoom = 15;

    protected $poligonPoints = [];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema([

                        Fieldset::make('Owner Information')
                            ->schema([
                                Forms\Components\TextInput::make('owner_firstname')
                                    ->label('Frist Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(4),

                                Forms\Components\TextInput::make('owner_lastname')
                                    ->label('Last Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(4),
                                Forms\Components\TextInput::make('owner_middle')
                                    ->label('Middle Name')
                                    ->maxLength(255)
                                    ->columnSpan(3),
                                Forms\Components\TextInput::make('owner_suffix')
                                    ->label('Suffix')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('contact_number')
                                    ->label('Contact Number:')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(4),
                            ])
                            ->columns(12)
                            ->columnSpan(12),
                        Fieldset::make('Farm Information')
                            ->schema(
                                [
                                    // Forms\Components\TextInput::make('farm_type')
                                    //     ->required()
                                    //     ->maxLength(255)->columnSpan(4),
                                    Forms\Components\Select::make('farm_type')
                                        ->searchable()
                                        ->options(Animal::orderBy('animal_name')->pluck('animal_name', 'id'))
                                        ->columnSpan(4),
                                    Forms\Components\TextInput::make('farm_address')
                                        ->label("Farm other Info")
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpan(8),
                                    Section::make()
                                        ->schema([
                                            Forms\Components\Select::make('farm_municipality')
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
                                                ->required()->columnSpan(12),
                                            Forms\Components\Select::make('farm_barangay')
                                                ->label("Barangay")
                                                ->searchable()
                                                ->options(fn($get) =>  Barangay::where('municipality_id', $get('farm_municipality'))->get()->pluck('barangay_name', 'id')->toArray())
                                                ->columnSpan(12),
                                            Forms\Components\TextInput::make('latitude')
                                                ->required()
                                                ->hidden()
                                                ->reactive()
                                                ->default(125.9632)
                                                ->columnSpan(6),
                                            Forms\Components\TextInput::make('longitude')
                                                ->required()
                                                ->hidden()
                                                ->reactive()
                                                ->default(7.6038)
                                                ->columnSpan(6),

                                        ])
                                        ->columnSpan(4)
                                        ->columns(12),
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
                                        ->columnSpan(8)

                                ]
                            )
                            ->columns(12)->columnSpan(12),
                    ])
                    ->columns(12),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('owner_firstname')
                    ->label('First Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('owner_middle')
                    ->label('Middle Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('owner_lastname')
                    ->label('Last Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('owner_suffix')
                    ->label('Suffix')
                    ->searchable(),
                Tables\Columns\TextColumn::make('animal.animal_name')
                    ->label("Farm Type")
                    ->searchable(),
                Tables\Columns\TextColumn::make('farm_status')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('farm_municipality')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('farm_barangay')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('farm_address')
                    ->label('Farm Other Info')
                    ->searchable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('latitude')
                    ->searchable()->toggleable(isToggledHiddenByDefault: true),

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
                Tables\Actions\EditAction::make(),
                Action::make('status')
                    ->form([
                        Select::make('farm_status')
                            ->options([
                                'Application' => 'Application',
                                'For Survey' => 'For Survey',
                                'Registered' => 'Registered'
                            ])
                    ])
                    ->modalWidth("md")
                    ->action(function (array $data, $record) {
                        $record->farm_status = $data["farm_status"];
                        $record->save();
                    })
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
            'index' => Pages\ListRegisteredFarms::route('/'),
            'create' => Pages\CreateRegisteredFarm::route('/create'),
            'edit' => Pages\EditRegisteredFarm::route('/{record}/edit'),
        ];
    }
}
