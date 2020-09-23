<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceQuestion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'service_id',
        'question',
        'provider_name',
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
     * The service that belong to the question.
     */
    public function service()
    {
        return $this->belongsTo('App\ServiceType');
    }

    /**
     * The answers that belong to the question.
     */
    public function answers()
    {
        return $this->hasMany('App\ServiceAnswer', 'question_id');
    }

    /**
     * The answers that belong to the question.
     */
    public function all_answers()
    {
        return $this->hasMany('App\ServiceAnswer', 'question_id')->count();
    }

    /**
     * The activated answers that belong to the question.
     */
    public function active_answers()
    {
        return $this->hasMany('App\ServiceAnswer', 'question_id')->where('status', 1)->count();
    }

    /**
     * The deactivated answers that belong to the question.
     */
    public function deactive_answers()
    {
        return $this->hasMany('App\ServiceAnswer', 'question_id')->where('status', 0)->count();
    }
}
