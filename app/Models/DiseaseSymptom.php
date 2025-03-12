<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiseaseSymptom extends Model
{
    use HasFactory;
    protected $guarded  = [];
    public function disease()
    {
        return $this->belongsTo(Disease::class, "disease_id", "id");
    }
    public function Symptomx()
    {
        return $this->belongsTo(Symptom::class, 'symptom_id', 'id');
    }
}
