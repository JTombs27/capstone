<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Barangay;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Municipality;
use Filament\Resources\Resource;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BarangayResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BarangayResource\RelationManagers;

class BarangayResource extends Resource
{
    protected static ?string $model = Barangay::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = "USER MANAGEMENT";
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('municipality_id')
                    ->label("Municipality")
                    ->options(Municipality::all()->pluck('municipality_name', 'id'))
                    ->searchable()
                    ->native(false)
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('barangay_name')
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
                Tables\Columns\TextColumn::make('municipal.municipality_name')
                    ->label("Municipality")
                    ->sortable(),
                Tables\Columns\TextColumn::make('barangay_name')
                    ->sortable()
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
                SelectFilter::make("municipality_id")
                    ->label("Filter By Municipality")
                    ->options(Municipality::all()->pluck('municipality_name', 'id'))
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangays::route('/'),
            // 'create' => Pages\CreateBarangay::route('/create'),
            //'edit' => Pages\EditBarangay::route('/{record}/edit'),
        ];
    }
}
