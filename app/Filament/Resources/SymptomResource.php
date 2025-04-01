<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Animal;
use App\Models\Symptom;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SymptomResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SymptomResource\RelationManagers;

class SymptomResource extends Resource
{
    protected static ?string $model = Symptom::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = "UTILITIES";
    protected static ?string $navigationLabel = "Symptoms";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Select::make('animal_id')
                    ->label("Select Animal")
                    ->options(Animal::where("animal_name", "!=", "All")->pluck('animal_name', 'id'))
                    ->searchable()
                    ->native(false)
                    ->required(),
                Forms\Components\TextInput::make('symptom_descr')
                    ->label("Symptom")
                    ->required()
                    ->columns(12)
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('symptom_descr')
                    ->label("LIST OF ALL SYMPTOMS")
                    ->searchable(),

            ])
            ->filters([
                //
                SelectFilter::make("animal_id")
                    ->label("Filter By Animals")
                    ->options(Animal::where('animal_name', "!=", "All")->pluck('animal_name', 'id'))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ])
        ;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSymptoms::route('/'),
        ];
    }
}
