<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelplineSymptom extends Model
{
    use HasFactory;
    protected $guarded  = [];

    public function symptoms()
    {
        $this->belongsTo(Symptom::class, "symptom_id", "id");
    }
}
