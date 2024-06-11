<?php

use App\Models\Exchange;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('conversation.{exchangeID}.{recipientId}' , function ($user , $exchangeID){
    return Exchange::where('id' , $exchangeID)->where(fn($query) =>
        $query->where('exchange_user_id' , $user->id)->orWhere('owner_user_id' , $user->id)
    )->exists();
});
