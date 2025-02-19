<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Disease;
use Filament\Forms\Set;
use App\Models\Helpline;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Dotswan\MapPicker\Fields\Map;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\HelplineResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\HelplineResource\RelationManagers;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class HelplineResource extends Resource
{

    protected static ?string $model = Helpline::class;

    protected static ?string $navigationGroup = "SERVICES";
    protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';
    protected static ?string $navigationLabel = "Animal Helpline";
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
                Forms\Components\TextInput::make('query_municipality')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('query_barangay')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('query_address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('longgitude')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('latitude')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('contact_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('animal_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('other_info')
                    ->required()
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('animal.animal_name'),
                Tables\Columns\TextColumn::make('disease.disease_description')
                    ->label("Disease")
                    ->getStateUsing(fn($record) => $record->disease->disease_description ?? 'Unverified Disease'),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
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
                    // Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make()
                        ->slideOver(),
                    Tables\Actions\DeleteAction::make(),
                    Action::make("set_location")
                        ->icon("heroicon-s-map")
                        ->form(
                            [
                                Section::make("")
                                    ->schema([
                                        // TextInput::make('latitude')
                                        //     ->label("Latitude:")
                                        //     ->reactive()
                                        //     ->readOnly()
                                        //     ->columnSpan(6),
                                        // TextInput::make('longitude')
                                        //     ->label("Longitude:")
                                        //     ->reactive()
                                        //     ->readOnly()
                                        //     ->columnSpan(6),
                                        Map::make('location')
                                            ->label('Location')
                                            ->columnSpan(12)
                                            ->defaultLocation(latitude: 7.6038, longitude: 125.9632)
                                            ->afterStateUpdated(function (Set $set, ?array $state) {
                                                // if ($state) {
                                                //     $set('latitude',  $state['lat']);
                                                //     $set('longitude', $state['lng']);
                                                // }
                                            })
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

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
