<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CityProduct extends Model
{
    protected $table = 'cities_products';

    public function city() {
        return $this->belongsTo(City::class);
    }
}
