<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Symptom extends Model
{
    use HasFactory;
    protected $table    = "symptoms";
    protected $guarded  = [];

    public function diseaseSymptoms()
    {
        return $this->hasMany(DiseaseSymptom::class, 'symptom_id', 'id');
    }

    public function animal()
    {
        return $this->belongsTo(Animal::class, "animal_id", "id");
    }
}
