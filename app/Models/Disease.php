<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Disease extends Model
{
    use HasFactory;
    protected $table    = "diseases";
    protected $guarded  = [];

    public function diseaseSymptoms()
    {
        return $this->hasMany(DiseaseSymptom::class, 'disease_id', 'id');
    }


    public function Animal()
    {
        return $this->belongsTo(Animal::class, "animal_id", "id");
    }
}
