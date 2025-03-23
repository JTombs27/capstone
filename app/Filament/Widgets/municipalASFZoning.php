<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\ASFZoning;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class municipalASFZoning extends BaseWidget
{
    protected static bool $isLazy = false; // Ensures the table loads immediately
    public function table(Table $table): Table
    {
        return $table
            ->query(
                ASFZoning::with("municipality")
            )
            ->columns([
                TextColumn::make('municipality.municipality_name')
                    ->label('Municipality Name')
                    ->default('N/A'),
                TextColumn::make('remarks')
                    ->label('Status')
                    ->default('N/A')

            ])
            ->defaultPaginationPageOption(25)
            ->paginated([25])
            ->emptyStateHeading('No records found') // Custom empty message
            ->emptyStateDescription('No configuration found for Municipal ASF Zoning.');
    }

    // Override this method to remove the heading
    protected static ?string $heading = "";
}
