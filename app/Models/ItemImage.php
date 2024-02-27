<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class ItemImage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function setImageAttribute ($image){
        $newImageName = uniqid() . '_' . 'image' . '.' . $image->extension();
        $image->move(public_path('item_images') , $newImageName);
        return $this->attributes['image'] =  '/'.'item_images'.'/' . $newImageName;
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($itemImage) {
            $imagePath = public_path($itemImage->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        });
    }
}
