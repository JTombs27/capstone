<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barangay extends Model
{
    use HasFactory;
    protected $table    = "barangays";
    protected $guarded  = [];

    public function Municipal()
    {
        return $this->belongsTo(Municipality::class, "municipality_id", "id");
    }
}
