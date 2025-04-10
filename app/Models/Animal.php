<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Animal extends Model
{
    use HasFactory;
    protected $table    = "animals";
    protected $guarded  = [];

    public function SymptomsRelationManager()
    {
        return $this->hasMany(Symptom::class, "animal_id", "id");
    }
}
