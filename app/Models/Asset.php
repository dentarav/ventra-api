<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = ['name', 'category', 'status', 'condition'];
    public function category() { return $this->belongsTo(Category::class); }
    public function location() { return $this->belongsTo(Location::class); }
}
