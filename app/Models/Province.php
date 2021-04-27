<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'provinces';
    public $timestamps = false;
    protected $guarded = array();

    public function user()
	{
		return $this->belongsToMany(User::class);
    }
    
}