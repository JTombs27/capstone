<?php

namespace App\Filament\Resources\AnimalResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Animal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class SymptomsRelationManagerRelationManager extends RelationManager
{
    protected static string $relationship   = 'SymptomsRelationManager';

    // Corrected getTitle method signature
    public static function getTitle($ownerRecord, string $pageClass): string
    {
        // Check if $ownerRecord is an instance of Animal model
        if ($ownerRecord instanceof Animal) {
            // Return the title dynamically based on the animal's name
            return "{$ownerRecord->animal_name} List of Symptoms";
        }

        // Fallback title in case the record is not an Animal instance
        return 'Animal Symptoms';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('symptom_descr')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('symptom_descr')
            ->columns([
                Tables\Columns\TextColumn::make('symptom_descr')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label("Add Symptom"),
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
