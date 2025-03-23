<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ASFZoning extends Model
{
    use HasFactory;
    protected $table    = "asfzoning";
    protected $guarded  = [];

    protected $fillable = [
        'municipality_id',
        'color_code',
        'remarks',
        'ryear',
        'rmonth',
    ];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }
}
