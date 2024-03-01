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

    public function scopeFilter($query , array $filters){
        $query->when($filters['country_id'] ?? false , fn($query , $country_id) =>
            $query->whereHas('area' , fn($query) =>
                $query->whereHas('city' , fn($query) =>
                    $query->where('country_id' , $country_id)
                )
            )
        );

        $query->when($filters['city_id'] ?? false , fn($query , $city_id) =>
            $query->whereHas('area' , fn($query) =>
                $query->where('city_id' , $city_id)
            )
        );
        $query->when($filters['area_id'] ?? false , fn($query , $area_id) =>
            $query->where('area_id', $area_id)
        );
        $query->when($filters['search_text'] ?? false , fn($query , $search_text) =>
            $query->where('title', 'Like' ,'%'. $search_text . '%')
            ->orWhere('description' ,  'Like' ,'%'. $search_text . '%')
        );

        $query->when($filters['status'] ?? false , fn($query , $status) =>
            $query->where('status', $status)
        );
    }
}
