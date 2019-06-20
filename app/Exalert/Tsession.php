<?php

namespace App\Exalert;

use Illuminate\Database\Eloquent\Model;

class Tsession extends Model
{
    protected $fillable =['user_id', 'chat_id', 'command', 'method', 'done'];
}
