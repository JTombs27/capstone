<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempMonth extends Model
{
    use HasFactory;
    protected $table    = "temp_month_tbl";
    protected $guarded  = [];
}

