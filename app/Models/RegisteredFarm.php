<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegisteredFarm extends Model
{
    use HasFactory;
    protected $guarded  = [];
    //protected $fillable = ['owner_firstname', 'owner_lastname', 'owner_fi'];
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
        return $this->belongsTo(Animal::class, "farm_type", "id");
    }
}
