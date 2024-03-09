<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\File;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function setImageAttribute ($image){
        $newImageName = uniqid() . '_' . 'image' . '.' . $image->extension();
        $image->move(public_path('user_images') , $newImageName);
        return $this->attributes['image'] =  '/'.'user_images'.'/' . $newImageName;
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($user) {
            $imagePath = public_path($user->image);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        });
    }

    public function verificationCodes() {
        return $this->hasMany(VerificationCode::class);
    }

    public function userDevices(){
        return $this->hasMany(UserDevice::class);
    }

    public function notification(){
        return $this->hasMany(Notification::class , 'notified_user_id');
    }

    public function unReadNotification(){
        return $this->notifications()->count();
    }
}
