<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Animal;
use App\Models\Disease;
use App\Models\Symptom;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DiseaseResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DiseaseResource\RelationManagers;

class DiseaseResource extends Resource
{
    protected static ?string $model = Disease::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup = "UTILITIES";

    public static function getBreadcrumbs(): array
    {
        return [
            'index' => 'Helpline List', // 👈 Customize the "List" page breadcrumb
            'create' => 'Report a Case', // 👈 Customize the "Create" page breadcrumb
            'edit' => 'Edit Case Details', // 👈 Customize the "Edit" page breadcrumb
        ];
    }

    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                //
                Section::make("Disease Information")
                    ->schema([
                        Forms\Components\Checkbox::make("contigouse")
                            ->id("disease-checkbox")
                            ->label("Contiguous Disease?")
                            ->inline(false)
                            ->columnspan(2),
                        Forms\Components\Select::make('animal_id')
                            ->label("Select Animal")
                            ->options(Animal::where("animal_name", "!=", "All")->pluck('animal_name', 'id'))
                            ->searchable()
                            ->native(false)
                            ->reactive() // Trigger updates when this field changes
                            ->afterStateUpdated(fn(callable $set) => $set('diseasesymptoms', []))
                            ->required()
                            ->columnspan(4),
                        Forms\Components\Textinput::make('disease_description')
                            ->label("Diseas Description")
                            ->required()
                            ->columnspan(4),
                        Select::make('disease_type')
                            ->options([
                                'Viral' => 'Viral',
                                'Bacterial' => 'Bacterial',
                                'Parasitic Worm' => 'Parasitic Worm',
                            ])
                            ->required()
                            ->columnspan(2),
                        Textarea::make("treatment")
                            ->required()
                            ->columnspan(6),
                        Textarea::make("preventions")
                            ->required()
                            ->columnspan(6)
                    ])
                    ->columns(12),
                Section::make('List Of Symptoms')
                    ->schema([
                        Forms\Components\CheckboxList::make("diseasesymptoms")
                            ->label('')
                            ->options(fn($get) =>  Symptom::where('animal_id', $get('animal_id'))->get()->pluck('symptom_descr', 'id'))
                            ->gridDirection('row')
                            ->reactive() // Make this field reactive
                            ->columns(4)
                            ->bulkToggleable()
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $component->state(
                                        DB::table('disease_symptoms')
                                            ->where('disease_id', $record->id)
                                            ->pluck('symptom_id')
                                            ->toArray()
                                    );
                                }
                            })
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                // Tables\Columns\TextColumn::make('animal.animal_name')
                //     ->label("For Animals")
                //     ->searchable(),
                Tables\Columns\TextColumn::make('disease_description')
                    ->description(fn($record): string => $record->disease_type)
                    ->label("Disease Description")
                    ->searchable()
                    ->extraCellAttributes(['class' => 'dictionary-cell', 'style' => 'width: 20%;'])
                    ->wrap(),
                Tables\Columns\TextColumn::make('diseaseSymptoms.Symptomx.symptom_descr')
                    ->extraCellAttributes(['class' => 'dictionary-cell', 'style' => 'width: 25%;'])
                    ->label("Symptoms")
                    ->searchable()
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->bulleted()
                    ->wrap(),
                Tables\Columns\TextColumn::make('treatment')
                    ->label("Treatment")
                    ->searchable()
                    ->extraCellAttributes(['class' => 'dictionary-cell'])
                    ->wrap(),
                Tables\Columns\TextColumn::make('preventions')
                    ->label("Preventions")
                    ->searchable()
                    ->extraCellAttributes(['class' => 'dictionary-cell'])
                    ->wrap(),


            ])
            ->filters([
                //
                SelectFilter::make("animal_id")
                    ->label("Filter By Animals")
                    ->options(Animal::whereNot("animal_name", "All")->pluck('animal_name', 'id'))
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Action::make("Print")
                        ->icon("heroicon-s-printer")
                        ->color("success")
                        ->modalWidth("3xl")
                        ->modalContent(
                            fn($record) => view(
                                'livewire.report-viewer',
                                [
                                    'report_iframe' => "http://hris.dvodeoro.local:8048/CrystalReportMVC/ViewReport?par_value=$record->id"
                                ]

                            )
                        )
                        ->slideOver()
                        ->modalAlignment(Alignment::Center),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->icon("heroicon-s-cog-6-tooth")
            ])
        ;
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
            //RelationManagers\SymptomsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiseases::route('/'),
            'create' => Pages\CreateDisease::route('/create'),
            'edit' => Pages\EditDisease::route('/{record}/edit'),
        ];
    }
}
