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
                ASFZoning::query()
                    ->join('municipalities', 'asfzoning.municipality_id', '=', 'municipalities.id')
                    ->orderBy('municipalities.municipality_name')
                    ->select('asfzoning.*') // Make sure to select ASFZoning fields to avoid conflicts
                    ->with('municipality')
            )
            ->columns([
                TextColumn::make('municipality.municipality_name')
                    ->label('Municipality')
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

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'zoom' => [
                    'pan' => [
                        'enabled' => true,
                        'mode' => 'x', // Horizontal panning
                    ],
                    'zoom' => [
                        'enabled' => true,
                        'mode' => 'x', // Zoom along x-axis
                    ],
                ],
            ],
        ];
    }
}
