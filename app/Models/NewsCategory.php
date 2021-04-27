<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model
{
	protected $table = 'news_category';
    public function news()
	{
		return $this->hasMany(News::class);
	}
}