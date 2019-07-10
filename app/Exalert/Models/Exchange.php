<?php

namespace App\Exalert\Models;

use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    //primary key id string  = pair  'BTC_USD' etc

    public $incrementing = false;

    protected $fillable = ['id', 'buy', 'sell', 'diff'];
}
