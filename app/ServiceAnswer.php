<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceAnswer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question_id',
        'answer',
        'price',
        'fixed',
        'minute',
        'hour',
        'distance',
        'calculator',
        'description',
        'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    /**
     * The services that belong to the user.
     */
    public function question()
    {
        return $this->belongsTo('App\ServiceQuestion', 'question_id');
    }
}
