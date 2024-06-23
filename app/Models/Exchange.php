<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function ownerUser(){
        return $this->belongsTo(User::class , 'owner_user_id');
    }

    public function exchangeUser(){
        return $this->belongsTo(User::class , 'exchange_user_id');
    }
}
