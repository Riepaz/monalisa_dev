<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';
    protected $guarded = array();
    
    public function NewsCategories()
    {
        return $this->belongsTo(NewsCategory::class);
    }
}