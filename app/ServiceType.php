<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'provider_name',
        'image',
        'price',
        'fixed',
        'description',
        'status',
        'minute',
        'hour',
        'distance',
        'calculator',
        'capacity',
        'weight',
        'width',
        'height',
        'type'
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
    public function questions()
    {
        return $this->hasMany('App\ServiceQuestion', 'service_id');
    }

    /**
     * The services that belong to the user.
     */
    public function all_questions()
    {
        return $this->hasMany('App\ServiceQuestion', 'service_id')->count();
    }

    /**
     * The services that belong to the user.
     */
    public function active_questions()
    {
        return $this->hasMany('App\ServiceQuestion', 'service_id')->where('status', 1)->count();
    }

    /**
     * The services that belong to the user.
     */
    public function deactive_questions()
    {
        return $this->hasMany('App\ServiceQuestion', 'service_id')->where('status', 0)->count();
    }
}
