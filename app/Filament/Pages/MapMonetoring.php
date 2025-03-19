<?php

namespace App\Filament\Pages;

use App\Models\Animal;
use App\Models\Barangay;
use App\Models\Helpline;
use Filament\Pages\Page;
use Livewire\Attributes\On;
use App\Models\Municipality;
use App\Models\RegisteredFarm;
use Filament\Pages\Actions\Action;
use App\Filament\Widgets\DiseaseInfo;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\BlogPostsChart;
use Filament\Notifications\Notification;
use App\Filament\Resources\HelplineResource\Widgets\MonitoringStats;
use App\Models\SMSNotification;
use App\Services\SmsService;

class MapMonetoring extends Page
{

    protected static string $view               = 'filament.pages.map-monetoring';

    protected static ?string $navigationLabel   = 'Map Monitoring';
    protected static ?string $slug              = 'map-monitoring';
    protected static ?string $navigationIcon    = 'heroicon-o-map';
    protected ?string $heading                  = "";
    public $farm_types;

    public $selectedDisease = [];
    public $registeredFarms = [];
    protected function getListeners(): array
    {

        return ['open-disease-modal' => 'handleOpenDiseaseModal'];
    }

    public function handleOpenDiseaseModal($details)
    {
        $this->selectedDisease = $details;
        //dd($details);
        $brangayid = $this->selectedDisease["query_barangay"];
        $this->registeredFarms = RegisteredFarm::with('animal')->where("farm_barangay", "=", $brangayid)->get();
        //dd($this->registeredFarms[0]["owner_firstname"]);
        $this->dispatch('open-modal', id: 'diseaseInfo');
    }

    public function sendSMS($id, $helpline_id)
    {
        $farm = collect($this->registeredFarms)->firstWhere('id', $id);

        // Send SMS using the SmsService
        $smsService = new SmsService();
        $phoneNumber = $farm['contact_number'];
        $message = "Provincial Veterinary Office of the Province of Davao de Oro. We do inform you to do biosecurity measure, Animal disease Reported around your barangay.";
        $response = $smsService->sendSMS($phoneNumber, $message);
        //dd($response);
        // Check response (you can customize this based on Semaphore API response)
        if (($response[0]['status'] == "queued" || $response[0]['status'] == "Pending")) {
            SMSNotification::create([
                'helpline_id'   => $helpline_id,
                'farm_id'       => $farm['id'],
                'phone_number'  => $phoneNumber,
                'message'       => $message,
                'status'        => $response[0]['status'], // Initial status
                'message_id'    => $response[0]['message_id'] ?? null, // Save Message ID
            ]);

            Notification::make()
                ->title('Notification Sent!')
                ->success()
                ->body($farm['owner_firstname'] . ' ' . $farm['contact_number']  . ' successfully notified.')
                ->seconds(3)
                ->icon('heroicon-o-bell') // Optional icon
                ->send();
        } else {
            Notification::make()
                ->title('SMS Failed')
                ->danger()
                ->body('Could not send SMS. Please try again.')
                ->send();
        }
    }

    public function checkSmsStatus($messageId)
    {
        $smsService = new SmsService();
        $statusResponse = $smsService->getSmsStatus($messageId);

        if (isset($statusResponse['status'])) {
            // Update status in the database
            SMSNotification::where('message_id', $messageId)->update(['status' => $statusResponse['status']]);

            Notification::make()
                ->title('SMS Status Updated')
                ->success()
                ->body('The SMS status is now: ' . $statusResponse['status'])
                ->send();
        }
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
        $data = Helpline::with(["disease", "animal", "municipal", "barangay"])->where("status", "Monitored")->get();

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
