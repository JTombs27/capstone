<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Helpline extends Model
{
    use HasFactory;
    protected $guarded  = [];

    public function animal()
    {
        return $this->belongsTo(Animal::class, "animal_id", "id");
    }

    public function disease()
    {
        return $this->belongsTo(Disease::class, "disease_id", "id");
    }
}
