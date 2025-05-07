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
        return $this->belongsTo(Symptom::class, "symptom_id", "id");
    }

    public function Symptomx()
    {
        return  $this->belongsTo(Symptom::class, "symptom_id", "id");
    }

    public function helpline()
    {
        return $this->belongsTo(Helpline::class, "helpline_id", "id");
    }
}
