<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Animal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Navigation\NavigationItem;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AnimalResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AnimalResource\RelationManagers;

class AnimalResource extends Resource
{
    protected static ?string $model = Animal::class;

    protected static ?string $navigationIcon  = 'icon-animals';
    protected static ?string $navigationGroup = "LIBRARIES";
    protected static ?string $navigationLabel = "Serve Animals";
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('animal_name')
                    ->label("Animal Name")
                    ->required()
                    ->columns(12)
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->whereNot("animal_name", "All"))
            ->columns([
                //
                Tables\Columns\TextColumn::make('animal_name')
                    ->label("LIST OF ALL ANIMALS")
                    ->searchable(),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAnimals::route('/'),
        ];
    }
}
