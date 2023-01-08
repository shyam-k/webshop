<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Orders;

class Products extends Model
{
    use HasFactory;
/*
    public function orders()
    {
        return $this->belongsToMany(Orders::class);
    }*/
}
