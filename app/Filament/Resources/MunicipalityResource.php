<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Municipality;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MunicipalityResource\Pages;
use App\Filament\Resources\MunicipalityResource\RelationManagers;

class MunicipalityResource extends Resource
{
    protected static ?string $model = Municipality::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = "USER MANAGEMENT";
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('municipality_name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\ColorPicker::make('color')
                    ->columnSpan(3),
                Forms\Components\TextInput::make('report_threshold')
                    ->label("Threshold")
                    ->numeric()
                    ->required()
                    ->columnSpan(3),
                Forms\Components\TextInput::make('lat')
                    ->label("Latitude")
                    ->numeric()
                    ->required()
                    ->columnSpan(3),
                Forms\Components\TextInput::make('lng')
                    ->label("Longitude")
                    ->numeric()
                    ->required()
                    ->columnSpan(3),

            ])
            ->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('municipality_name')
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
            'index' => Pages\ManageMunicipalities::route('/'),
        ];
    }
}
