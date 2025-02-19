<?php

namespace App\Livewire;

use App\Models\Animal;
use App\Models\Symptom;
use Livewire\Component;
use App\Models\Barangay;
use App\Models\Helpline;
use App\Models\Municipality;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use App\Models\HelplineSymptom;
use Jantinnerezo\LivewireAlert\LivewireAlert;

#[Title('Help - DDO-ADS-TRACE')]

class AnimalHelp extends Component
{
    use LivewireAlert;
    //#[Url]
    public $municipality_id;
    public $symptomsAdded = [];
    public $symptomsApplication = [];
    public $applicationModel = [
        "user_id"               => null,
        "first_name"            => "",
        "last_name"             => "",
        "animal_id"             => "",
        "disease_id"            => null,
        "contact_number"        => "",
        "query_municipality"    => "",
        "query_barangay"        => "",
        "image_path"            => "",
        "query_address"         => "",
        "affected_count"        => 0,
        "death_count"           => null,
        "latitude"              => null,
        "longitude"             => null,
        "other_info"            => "",
        "status"                => "Application"
    ];
    public function render()
    {
        $municipalities = Municipality::all();
        $barangays      = Barangay::all();
        $barangays  = $barangays->where("municipality_id", $this->applicationModel["query_municipality"]);
        $animals    = Animal::all();
        $animals    = $animals->where("animal_name", "!=", "All");
        $symptoms   = Symptom::all();
        $symptoms = $symptoms->where("animal_id", $this->applicationModel["animal_id"]);
        return view('livewire.animal-help', [
            "municipality" => $municipalities,
            "barangays" => $barangays,
            "animals" => $animals,
            "symptoms" => $symptoms
        ]);
    }

    public function submitApplication()
    {
        $animal_id = $this->applicationModel["animal_id"];
        $this->symptomsApplication = [];

        // Create the parent record and get its ID
        $helpline = Helpline::create($this->applicationModel);

        // Prepare the array of child records
        foreach ($this->symptomsAdded as $value) {
            $this->symptomsApplication[] = [
                "symptom_id" => $value,
                "helpline_id" => $helpline->id // Use the ID from the created parent
            ];
        }

        // Insert multiple child records
        HelplineSymptom::insert($this->symptomsApplication);
        $this->alert('success', 'Application successfully submitted!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true
        ]);

        $this->applicationModel = [
            "user_id"               => null,
            "first_name"            => "",
            "last_name"             => "",
            "animal_id"             => "",
            "disease_id"            => null,
            "contact_number"        => "",
            "query_municipality"    => "",
            "query_barangay"        => "",
            "image_path"            => "",
            "query_address"         => "",
            "affected_count"        => 0,
            "death_count"           => null,
            "latitude"              => null,
            "longitude"             => null,
            "other_info"            => "",
            "status"                => "Application"
        ];
    }
    public function resetSymptoms()
    {
        $this->symptomsAdded = [];
    }
}
