<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $table = 'villages';
    public $timestamps = false;
    protected $guarded = array();

    public function user()
	{
		return $this->belongsT('App\Models\User', 'village_id', 'id');
	}
}