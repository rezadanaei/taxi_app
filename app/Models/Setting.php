<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
     use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'site_name',
        'merchant_id',  
        'sms_panel_number',
        'sms_panel_username',
        'sms_panel_password',
        'nashan_web_key',
        'nashan_service_key',
        'colers_primary',
        'colers_secondary',
        'colers_tertiary',
    ];
}
