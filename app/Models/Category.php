<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Category extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function setImageAttribute ($image){
        $newImageName = uniqid() . '_' . 'image' . '.' . $image->extension();
        $image->move(public_path('category_images') , $newImageName);
        return $this->attributes['image'] =  '/'.'category_images'.'/' . $newImageName;
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($category) {
            $imagePath = public_path($category->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        });
    }

    public function category(){
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
