<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Helpline extends Model
{
    use HasFactory;
    protected $guarded  = [];
    protected $casts = [
        'location' => 'json',
        'image_path' => 'json',
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            if (is_array($model->location)) {
                $model->latitude = $model->location["lat"];
                $model->longitude = $model->location["lng"];
                $model->location = json_encode($model->location);
            }
        });
    }

    public function animal()
    {
        return $this->belongsTo(Animal::class, "animal_id", "id");
    }
    public function municipal()
    {
        return $this->belongsTo(Municipality::class, "query_municipality", "id");
    }
    public function barangay()
    {
        return $this->belongsTo(Barangay::class, "query_barangay", "id");
    }


    public function disease()
    {
        return $this->belongsTo(Disease::class, "disease_id", "id");
    }

    public function helplineSymptoms()
    {
        return $this->hasMany(HelplineSymptom::class, "helpline_id", "id");
    }
}
