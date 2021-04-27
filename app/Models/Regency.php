<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regency extends Model
{
    protected $table = 'regencies';
    public $timestamps = false;
    protected $guarded = array();

    public function user()
	{
		return $this->belongsT('App\Models\User', 'villageregency_id', 'id');
	}
}