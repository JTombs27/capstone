<?php

namespace App\Filament\Pages;

use App\Models\Animal;
use App\Models\Barangay;
use App\Models\Helpline;
use Filament\Pages\Page;
use App\Models\Municipality;
use App\Models\RegisteredFarm;
use Filament\Pages\Actions\Action;
use App\Filament\Widgets\DiseaseInfo;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\BlogPostsChart;
use App\Filament\Resources\HelplineResource\Widgets\MonitoringStats;
use Livewire\Attributes\On;

class MapMonetoring extends Page
{

    protected static string $view               = 'filament.pages.map-monetoring';

    protected static ?string $navigationLabel   = 'Map Monitoring';
    protected static ?string $slug              = 'map-monitoring';
    protected static ?string $navigationIcon    = 'heroicon-o-map';
    protected ?string $heading                  = "";
    public $farm_types;

    public $selectedDisease = [];

    protected function getListeners(): array
    {

        return ['open-disease-modal' => 'handleOpenDiseaseModal'];
    }

    public function handleOpenDiseaseModal($disease)
    {
        $this->selectedDisease = $disease;
        // dd($this->selectedDisease);
        $this->dispatch('open-modal', id: 'privacy');
    }
    public function mount()
    {
        $this->farm_types = Animal::all()->sortBy('animal_name')->pluck('animal_name');
    }

    public function extractNameAndPopulationFromGeoJSON($geoJsonFilePath)
    {
        // Load the GeoJSON file
        $geoJson = file_get_contents($geoJsonFilePath);

        // Decode the JSON into an associative array
        $geoJsonData = json_decode($geoJson, true);
        $results = array_map(function ($feature) {
            $properties = $feature['properties'] ?? [];
            $name = $properties['Brgy'] ?? 'Unknown';
            $population = $properties['MUN'] ?? 0;

            return [
                'name' => $name,
                'population' => $population,
            ];
        }, $geoJsonData['features']);

        // Sort the results by population in descending order
        usort($results, function ($a, $b) {
            return $b['population'] <=> $a['population']; // Descending order
        });

        // Format the output as strings
        return array_map(function ($item) {
            return "['municipality_id'=>{$item['population']}, 'brangay_name'=> '{$item['name']}']";
        }, $results);

        return $results;
    }



    public function getFarms()
    {

        $data = RegisteredFarm::with('animal')->get()->each(function ($item) {
            $item->animal_name = $item->animal->animal_name;
        });
        return $data;
    }

    public function getDiseaseMonitored()
    {
        $data = Helpline::with("disease")->where("status", "Monitored")->get();
        return $data;
    }

    public function getBarangays()
    {

        $data = Barangay::all();
        return $data;
    }

    public function getMunicipalities()
    {

        $data = Municipality::all();
        return $data;
    }

    public static function getWidgets(): array
    {
        return [
            MonitoringStats::class,
        ];
    }


    protected function getHeaderWidgets(): array
    {
        return [
            MonitoringStats::class
        ];
    }
}
