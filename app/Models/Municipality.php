<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Municipality extends Model
{
    use HasFactory;
    protected $table    = "municipalities";
    protected $guarded  = [];
}
