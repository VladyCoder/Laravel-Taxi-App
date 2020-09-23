<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsCode extends Model
{

    protected $fillable = ['phone','code'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
