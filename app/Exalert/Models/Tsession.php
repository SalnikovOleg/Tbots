<?php

namespace App\Exalert\Models;

use Illuminate\Database\Eloquent\Model;

class Tsession extends Model
{
    protected $fillable =['user_id', 'chat_id', 'command', 'method', 'done', 'value', 'comment'];
}
