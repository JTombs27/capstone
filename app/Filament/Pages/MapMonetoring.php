<?php

namespace App\Filament\Pages;

use App\Models\Animal;
use Livewire\Component;
use App\Models\Barangay;
use App\Models\Helpline;
use Filament\Pages\Page;
use App\Models\ASFZoning;
use Livewire\Attributes\On;
use App\Models\Municipality;
use App\Services\SmsService;
use App\Models\RegisteredFarm;
use App\Models\SMSNotification;
use PhpParser\Node\Stmt\TryCatch;
use Filament\Pages\Actions\Action;
use App\Filament\Widgets\DiseaseInfo;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\BlogPostsChart;
use Filament\Notifications\Notification;
use App\Filament\Resources\HelplineResource\Widgets\MonitoringStats;

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
    public $municipalities  = [];

    public function getASFZoning()
    {

        $data = ASFZoning::with('municipality')->get();
        return $data;
    }

    protected function getListeners(): array
    {

        return ['open-disease-modal' => 'handleOpenDiseaseModal'];
    }

    public function loadDiseaseData($year_from, $year_to)
    {
        $data = $this->getDiseaseMonitoredData($year_from, $year_to);

        // You can store it in a Livewire public property if needed
        $this->monitoredDiseases = $data;
    }
    public function handleOpenDiseaseModal($details)
    {
        $this->selectedDisease = $details;

        $brangayid  =  $this->selectedDisease["query_barangay"];
        $help_id    =  $this->selectedDisease["id"];
        $this->registeredFarms = RegisteredFarm::with(['animal', 'smsNotifications' => function ($query) use ($help_id) {
            $query->where('helpline_id', $help_id);
        }])
            ->where("farm_barangay", "=", $brangayid)
            ->get();
        $this->dispatch('open-modal', id: 'diseaseInfo');
    }

    public function sendSMS($id, $helpline_id)
    {
        $farm = collect($this->registeredFarms)->firstWhere('id', $id);

        // Send SMS using the SmsService
        $smsService     = new SmsService();
        $phoneNumber    = $farm['contact_number'];
        $owner          = $farm['owner_firstname'];
        $message        = "Hi $owner, Provincial Veterinary Office of the Province of Davao de Oro. We do inform you to do biosecurity measure, Animal disease Reported around your barangay.";
        $response       = $smsService->sendSMS($phoneNumber, $message);
        // Check response (you can customize this based on Semaphore API response)
        $exist = SMSNotification::where('helpline_id', $helpline_id)->where('farm_id', $id)->first();
        try {

            if (($response[0]['status'] == "queued" || $response[0]['status'] == "Pending")) {
                if ($exist) {
                    $exist->update([
                        'status'        => $response[0]['status'], // Initial status
                        'message_id'    => $response[0]['message_id'] ?? null, // Save Message ID
                    ]);

                    Notification::make()
                        ->title('Notification Re-Sent!')
                        ->success()
                        ->body($farm['owner_firstname'] . ' ' . $farm['contact_number']  . ' successfully notified.')
                        ->seconds(3)
                        ->icon('heroicon-o-bell') // Optional icon
                        ->send();
                } else {
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
                }
            } else {
                dd($response);
                Notification::make()
                    ->title('SMS Failed')
                    ->danger()
                    ->body('Could not send SMS. Please try again.')
                    ->send();
            }
            $this->updateallSMS();
        } catch (\Throwable $th) {
            dd($th->getMessage(), $response);
            Notification::make()
                ->title('SMS Failed')
                ->danger()
                ->body('Could not send SMS. Please try again.')
                ->send();
            //throw $th;
        }
    }

    public function updateallSMS()
    {
        $smsService = new SmsService();
        // Get all SMS logs with pending status
        $smsLogs = SMSNotification::where('status', 'pending')->get();

        foreach ($smsLogs as $log) {
            if ($log->message_id) {
                $statusResponse = $smsService->getSmsStatus($log->message_id);

                if (isset($statusResponse[0]['status'])) {
                    // Update status in the database
                    $log->update(['status' => $statusResponse[0]['status']]);
                }
            }
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
        $this->municipalities = Municipality::all()->sortBy('municipality_name');
        $this->farm_types = Animal::all()->sortBy('animal_name')->pluck('animal_name');
        $this->updateallSMS();
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

    public function getDiseaseMonitored($year_from, $year_to)
    {
        $data = Helpline::with(["disease", "animal", "municipal", "barangay"])->where("status", "Monitored")
            ->whereYear('date_reported', '>=', $year_from)
            ->whereYear('date_reported', '<=', $year_to)->get();

        return $data;
    }

    public function getBarangays()
    {

        $data = Barangay::all();
        return $data;
    }

    public function getMunicipalities()
    {

        $this->municipalities = Municipality::all();
        return $this->municipalities;
    }

    // public static function getWidgets(): array
    // {
    //     return [
    //         MonitoringStats::class,
    //     ];
    // }


    // protected function getHeaderWidgets(): array
    // {
    //     return [
    //         MonitoringStats::class
    //     ];
    // }
}
