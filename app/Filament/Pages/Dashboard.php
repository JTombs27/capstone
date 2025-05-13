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
use Carbon\Carbon;
use Filament\Forms\Components\{TextInput, Repeater, Select, Section};
use Illuminate\Support\Facades\Date;

class Dashboard extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view            = 'filament.pages.dashboard';
    public $formData                        = [];
    public $filter_year_from;
    public $filter_year_to;

    protected function getListeners(): array
    {
        return ['openModalZone', 'save'];
    }

    public function mount()
    {
        $this->filter_year_from = 2000;
        $this->filter_year_to = now()->year; // or Carbon::now()->year
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
