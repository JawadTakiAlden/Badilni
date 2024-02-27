<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function area(){
        return $this->belongsTo(Area::class);
    }

    public function subCategory(){
        return $this->belongsTo(Category::class);
    }

    public function images(){
        return $this->hasMany(ItemImage::class);
    }
}
