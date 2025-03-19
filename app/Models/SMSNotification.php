<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SMSNotification extends Model
{
    use HasFactory;
    protected $table    = "sms_notifications";
    protected $guarded  = [];
}
