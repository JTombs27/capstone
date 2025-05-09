<?php

namespace App\Filament\Widgets;

use App\Models\Municipality;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Widgets\Concerns\InteractsWithFilters;

class diseaseChart extends ChartWidget
{
    protected static ?string $heading = 'Annual Disease  Monitoring Report';
    protected static ?string $maxHeight = "300px";
    protected function getFiltersFormSchema(): array
    {
        return [
            Select::make('year')
                ->label('Select Year')
                ->options([
                    '2023' => '2023',
                    '2024' => '2024',
                ])
                ->default(date('Y')),
        ];
    }

    protected function getData(): array
    {
        $year = $this->filters['year'] ?? date('Y');
        // Define fixed month labels (January to December)
        $months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];
        // Retrieve data: Group by month and disease type
        $diseaseCases = DB::table('helplines')
            ->Join('diseases', 'diseases.id', 'helplines.disease_id')
            ->selectRaw('DATE_FORMAT(helplines.date_reported, "%Y-%m") as monthX,MONTHNAME(helplines.date_reported) as month, diseases.disease_description, COUNT(*) as total_cases')
            ->where(DB::raw("year(helplines.date_reported)"),2025)
            ->groupBy('month', 'monthX', 'diseases.disease_description')
            ->orderBy('monthX')
            ->get();

        // Extract unique disease types
        $diseaseTypes = $diseaseCases->pluck('disease_description')->unique()->values()->toArray();

        // Initialize datasets
        $datasets = [];

        foreach ($diseaseTypes as $disease) {
            // Generate a random color for each disease
            $color = substr(md5($disease), 0, 6); // Generate a hex color
            $rgbaColor = $this->hex2rgba($color, 0.5); // Convert hex to RGBA with 0.5 opacity

            // Initialize data array with all months set to 0
            $dataPoints = array_fill_keys($months, 0);

            // Populate with actual values from database
            foreach ($diseaseCases as $case) {
                if ($case->disease_description === $disease) {
                    $dataPoints[$case->month] = $case->total_cases;
                }
            }

            // Add dataset for this disease
            $datasets[] = [
                'label' => $disease,
                'data' => array_values($dataPoints), // Ensure all months are included
                'fill' => true, // Enable fill effect
                'borderColor' => "#$color", // Border color (solid)
                'backgroundColor' => $rgbaColor, // Background with 0.5 opacity
                'tension' => 0.3,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $months,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'zoom' => [
                    'pan' => ['enabled' => true, 'mode' => 'x'],
                    'zoom' => ['enabled' => true, 'mode' => 'x'],
                ],
            ],
        ];
    }

    function hex2rgba($hex, $alpha = 1.0)
    {
        $hex = str_replace("#", "", $hex);
        if (strlen($hex) == 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return "rgba($r, $g, $b, $alpha)";
    }
    protected function getType(): string
    {
        return 'line';
    }
}
