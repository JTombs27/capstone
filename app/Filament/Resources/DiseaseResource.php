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
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DiseaseResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DiseaseResource\RelationManagers;

class DiseaseResource extends Resource
{
    protected static ?string $model = Disease::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup = "LIBRARIES";

    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                //
                Section::make("Disease Information")
                    ->schema([
                        Forms\Components\Checkbox::make("contigouse")
                            ->label("Contigouse Disease?")
                            ->columnspan(12),
                        Forms\Components\Select::make('animal_id')
                            ->label("Select Animal")
                            ->options(Animal::where("animal_name", "!=", "All")->pluck('animal_name', 'id'))
                            ->searchable()
                            ->native(false)
                            ->reactive() // Trigger updates when this field changes
                            ->afterStateUpdated(fn(callable $set) => $set('diseasesymptoms', []))
                            ->required()
                            ->columnspan(5),
                        Forms\Components\Textinput::make('disease_description')
                            ->label("Diseas Description")
                            ->required()
                            ->columnspan(7),
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
                        // ->searchable($searchable)
                        // ->afterStateHydrated(
                        //     fn(Component $component, string $operation, ?Model $record) => static::setPermissionStateForRecordPermissions(
                        //         component: $component,
                        //         operation: $operation,
                        //         permissions: $options,
                        //         record: $record
                        //     )
                        // )
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('animal.animal_name')
                    ->label("For Animals")
                    ->searchable(),
                Tables\Columns\TextColumn::make('disease_description')
                    ->label("Disease Description")
                    ->searchable(),

            ])
            ->filters([
                //
                SelectFilter::make("animal_id")
                    ->label("Filter By Animals")
                    ->options(Animal::whereNot("animal_name", "All")->pluck('animal_name', 'id'))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
