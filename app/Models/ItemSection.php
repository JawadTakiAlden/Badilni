<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ItemSection extends Pivot
{
    protected $guarded = ['id'];
}
