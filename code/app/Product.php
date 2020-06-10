<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    public function datas() {
        return $this->hasMany(CityProduct::class, 'product_id', 'id');
    }
}
