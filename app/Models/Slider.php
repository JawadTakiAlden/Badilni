<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Slider extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function setImageAttribute ($image){
        $newImageName = uniqid() . '_' . 'image' . '.' . $image->extension();
        $image->move(public_path('slider_images') , $newImageName);
        return $this->attributes['image'] =  '/'.'slider_images'.'/' . $newImageName;
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($slide) {
            $imagePath = public_path($slide->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        });
    }
}
