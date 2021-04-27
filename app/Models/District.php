<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = 'districts';
    public $timestamps = false;
    protected $guarded = array();

    public function user()
	{
		return $this->belongsT('App\Models\User', 'district_id', 'id');
	}
}