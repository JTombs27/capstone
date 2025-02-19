<?php

namespace App\Filament\Resources\DiseaseResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Symptom;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class SymptomsRelationManager extends RelationManager
{
    protected static string $relationship = 'Symptoms';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('symptom_id')
                    ->label("Add Diseases Symptoms")
                    ->options(Symptom::all()->pluck('symptom_descr', 'id'))
                    ->required(),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->allowDuplicates(false)
            ->recordTitleAttribute('Diseas Symptoms')
            ->heading(new HtmlString("<b>List Symptoms of This Disease</b>"))
            ->columns([
                Tables\Columns\TextColumn::make('symptom.symptom_descr'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
