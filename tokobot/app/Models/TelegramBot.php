<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramBot extends Model
{
    protected $fillable = [
        'name',
        'token',
        'username',
        'is_active',
    ];
}
