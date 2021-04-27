<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Info extends Model
{
    protected $table = 'info';
    protected $guarded = array();
    
    public function InfoCategories()
    {
        return $this->belongsTo(InfoCategory::class);
    }
}