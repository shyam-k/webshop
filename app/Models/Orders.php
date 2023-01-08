<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customers;
use App\Models\Products;

class Orders extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id','payed', 'created_at','products']; 
    /*
    protected $attributes = [
        'payed' => 0,
    ];
*/

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }

    public function products()
    {
        return $this->belongsToMany(Products::class);
    }

}
