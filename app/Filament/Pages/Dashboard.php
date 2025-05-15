<?php

namespace App\Filament\Pages;

use Filament\Forms;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\ASFZoning;
use Filament\Tables\Table;
use Livewire\Attributes\On;
use App\Models\Municipality;
use Illuminate\Support\Facades\DB;
use App\Filament\Widgets\DiseaseInfo;
use Filament\Forms\Contracts\HasForms;
use App\Filament\Widgets\StatsOverview;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Widgets\BlogPostsChart;
use App\Models\Helpline;
use App\Models\TempMonth;
use Carbon\Carbon;
use Filament\Forms\Components\{TextInput, Repeater, Select, Section};
use Illuminate\Support\Facades\Date;
use Phpml\Regression\LeastSquares;


class Dashboard extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view            = 'filament.pages.dashboard';
    public $formData                        = [];
    public $filter_year_from;
    public $filter_year_to;
    public $historicalData;
    public $forecastResults = [];
    public $predictions     = [];
    public $forcastMonts    = 0;
    public $diseaseTrends  = [];


    protected function getListeners(): array
    {
        return ['openModalZone', 'save', 'processForcastingData'];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Helpline::selectRaw('
                    diseases.disease_description as disease_name,
                    YEAR(date_reported) as year,
                    MONTH(date_reported) as month,
                    COUNT(*) as case_count
                ')
                    ->join('diseases', 'helplines.disease_id', '=', 'diseases.id')
                    ->whereBetween(DB::raw('YEAR(helplines.date_reported)'), [
                        $this->filter_year_from,
                        $this->filter_year_to,
                    ])
                    ->groupBy('disease_name', DB::raw('YEAR(date_reported)'), DB::raw('MONTH(date_reported)'))
                    ->orderBy('disease_name')
                    ->orderBy('year')
                    ->orderBy('month')
            )
            ->columns([
                TextColumn::make('disease_name')
                    ->label('Disease')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('month')
                    ->label('Month')
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::create()->month($state)->format('F')),
                TextColumn::make('year')
                    ->label('Year'),
                TextColumn::make('case_count')
                    ->label('No. of Cases')
                    ->alignEnd(),
            ])
            ->pagination(10);
    }


    public function mount()
    {
        $this->filter_year_from = 2025;
        $this->filter_year_to   = now()->year; // or Carbon::now()->year

        $this->historicalData = Helpline::selectRaw('
                                            diseases.disease_description as disease_name,
                                            YEAR(date_reported) as year,
                                            MONTH(date_reported) as month,
                                            COUNT(*) as case_count,
                                            diseases.id as disease_id
                                        ')
            ->join('diseases', 'helplines.disease_id', '=', 'diseases.id')
            ->groupBy('disease_name', 'disease_id', DB::raw('YEAR(date_reported)'), DB::raw('MONTH(date_reported)'))
            ->orderBy('disease_name')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        $this->processForcastingData();
        $this->diseaseData();
    }

    public function diseaseData()
    {
        $yearFrom   = $this->filter_year_from;
        $yearto     = $this->filter_year_to;
        $this->diseaseTrends = Helpline::selectRaw('
            diseases.disease_description as disease_name,
            YEAR(date_reported) as year,
            MONTH(date_reported) as month,
            COUNT(*) as case_count,
            diseases.id as disease_id
        ')
            ->join('diseases', 'helplines.disease_id', '=', 'diseases.id')
            ->whereBetween(DB::raw('YEAR(helplines.date_reported)'), [$yearFrom, $yearto])
            ->groupBy('disease_name', 'disease_id', DB::raw('YEAR(date_reported)'), DB::raw('MONTH(date_reported)'))
            ->orderBy('disease_name')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
    }

    public function updated($name, $value)
    {
        if ($name === 'filter_year_from' || $name === 'filter_year_to') {
            $this->processForcastingData();
            $this->diseaseData();
            $this->dispatch('filter-update', [
                'municipal_data' => $this->getMunicipalityChartData(),
                'forecast_data' => $this->forecastResults,
            ]);
        }
    }

    public function onYearChange() {}


    public function processForcastingData()
    {
        $currentYear  = $this->filter_year_to;
        $currentMonth = now()->month;

        // Determine if forecasting should run
        // if ($this->filter_year_to < $currentYear) {
        //     return;
        // }

        $grouped = $this->historicalData->groupBy('disease_name');
        // if($currentYear  != now()->year)
        // {
        //     $this->forecastResults = [];
        //     dd($this->historicalData);
        //     foreach ($this->historicalData as $x) 
        //     {
        //         $this->predictions[] = [
        //             'month' =>  Carbon::createFromDate($x->year, $x->month, 1)->format('F').'-'.$x->year,
        //             'value' => $x->case_count,
        //         ];
        //     }
    
        //     $this->forecastResults[] = [
        //         'disease'  => '---',
        //         'trend'    => 'Not Applicable',
        //         'forecast' => $this->predictions,
        //         'forecastDataPoints' =>0,
        //         'forcastedyear' => $currentYear
        //     ];
        // }
        // else
        // {
            foreach ($grouped as $disease => $records) 
            {
                $samples = [];
                $targets = [];

                foreach ($records as $record) {
                    $timeStep  = ($record->year * 12) + $record->month;
                    $samples[] = [$timeStep];
                    $targets[] = $record->case_count;
                }

                // Skip diseases with less than 2 data points
                if (count($samples) < 2) continue;

                $disease_id = $records[0]["disease_id"];
                $regression = new LeastSquares();
                $regression->train($samples, $targets);
                $this->predictions = [];
                $dataExisting = TempMonth::selectRaw(
                    '
                                            disease_id,
                                            COUNT(helplines.id) as count,
                                            month_name
                                        '
                )
                    ->leftJoin('helplines', function ($join) use ($currentYear, $disease_id) {
                        $join->on(DB::raw('MONTH(helplines.date_reported)'), '=', 'temp_month_tbl.month')
                            ->where(DB::raw('YEAR(helplines.date_reported)'), '=', $currentYear)
                            ->where('helplines.disease_id', '=', $disease_id); // Optional condition
                    })
                    ->where("month", "<=", $currentMonth)
                    ->groupBy('disease_id', 'month_name')
                    ->get();
                foreach ($dataExisting as $x) {
                    $this->predictions[] = [
                        'month' => $x->month_name,
                        'value' => $x->count,
                    ];
                }
                // Forecast only future months in current year
                $startMonth = $this->filter_year_to == $currentYear ? $currentMonth + 1 : 13;
                if ($startMonth > 12) continue;

                for ($month = $startMonth; $month <= 12; $month++) {
                    $timeStep  = ($currentYear * 12) + $month;
                    $predicted = $regression->predict([$timeStep]);

                    $this->predictions[] = [
                        'month' => Carbon::createFromDate($currentYear, $month, 1)->format('F'),
                        'value' => round($predicted),
                    ];
                }

                if (empty($this->predictions)) continue;

                $lastActual = $this->predictions[array_key_last($this->predictions)];
                $trend      = $this->predictions[$startMonth - 1]['value'] > $lastActual ? 'Decreasing' : 'Increasing';



                $this->forecastResults[] = [
                    'disease'  => $disease,
                    'trend'    => $trend,
                    'forecast' => $this->predictions,
                    'forecastDataPoints' => $startMonth,
                    'forcastedyear' => $currentYear
                ];
            }
        //}
    }

    public function getMunicipalityChartData()
    {
        $yearFrom = $this->filter_year_from;
        $yearto = $this->filter_year_to;
        $data = Municipality::selectRaw(
            '
                municipalities.municipality_name AS municipality,
                COUNT(helplines.id)              AS value,
                municipalities.color             AS color
            '
        )
            ->leftJoin("helplines", function ($join) use ($yearFrom, $yearto) {
                $join->on("helplines.query_municipality", "municipalities.id")
                    ->whereBetween(DB::raw('YEAR(helplines.date_reported)'), [$yearFrom, $yearto]);
            })
            ->groupBy('municipality', 'color')
            ->orderBy('municipality')
            ->get();
        return $data;
    }


    public function openModalZone()
    {
        $this->dispatch('open-modal', id: 'modalZone');
    }

    public function getMunicipalities()
    {

        $data = ASFZoning::with('municipality')->get();
        return $data;
    }

    public function save()
    {
        $data = $this->form->getState();

        if (!empty($data['zone'])) {
            DB::beginTransaction(); // Ensure safe database transactions

            try {
                foreach ($data['zone'] as $zone) {
                    ASFZoning::create([
                        'municipality_id' => $zone['municipality_id'],
                        'color_code' => $zone['color_code'],
                        'remarks' => $zone['remarks'],
                        'ryear' => $zone['ryear'],
                        'rmonth' => $zone['rmonth'] ?? null, // Handle null months
                    ]);
                }

                DB::commit(); // Save data to the database

                // Close the modal after saving
                $this->dispatch('close-modal', id: 'modalZone');

                // Show success message
                session()->flash('success', 'ASF Zoning data saved successfully!');
            } catch (\Exception $e) {
                DB::rollback(); // Rollback on error
                session()->flash('error', 'Something went wrong: ' . $e->getMessage());
            }
        }
    }

    public function form(Form $form): Form
    {

        return $form
            ->schema([
                Repeater::make('zone')
                    ->label("Municipal Zoning")
                    ->schema([
                        Select::make('municipality_id')
                            ->label('Municipality')
                            ->searchable()
                            ->native(false)
                            ->options(Municipality::orderBy('municipality_name')->pluck('municipality_name', 'id'))
                            ->required()
                            ->columnSpan(8),

                        Select::make('color_code')
                            ->label('Select Color')
                            ->options([
                                'red' => 'Red',
                                '#FF33A1' => 'Pink',
                                'yellow' => 'Yellow',
                            ])
                            ->reactive()
                            ->required()
                            ->columnSpan(4)
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                // Find the current repeater row
                                $index = $get('zone') ? array_search($state, array_column($get('zone'), 'color_code')) : null;

                                if ($index !== false) {
                                    $remarks = match ($state) {
                                        'red' => "Infected Zone.",
                                        'yellow' => "Surveillance zone (ASF Free).",
                                        '#FF33A1' => "Buffer zone (ASF Free).",
                                        default => "",
                                    };

                                    // Update remarks for the same repeater row
                                    $set("remarks", $remarks);
                                }
                            }),

                        TextInput::make('ryear')
                            ->label("Report Year")
                            ->default("2025")
                            ->readOnly()
                            ->columnSpan(2),

                        TextInput::make('rmonth')
                            ->label("Report Month")
                            ->readOnly()
                            ->columnSpan(2),

                        TextInput::make('remarks')
                            ->label("Remarks")
                            ->reactive()
                            ->columnSpan(8),
                    ])
                    ->addActionLabel('Add Municipality')
                    ->columns(12)
                    ->default([]) // Ensures repeater initializes as an empty array
            ])
            ->statePath('formData');
    }
}
