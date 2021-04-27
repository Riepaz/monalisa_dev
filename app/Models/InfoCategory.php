<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoCategory extends Model
{
	protected $table = 'info_category';
    public function info()
	{
		return $this->hasMany(Info::class);
	}
}