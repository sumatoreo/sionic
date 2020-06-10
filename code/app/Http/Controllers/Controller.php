<?php

namespace App\Http\Controllers;

use App\City;
use App\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index() {
        $cities = City::orderBy('name', 'DESC')
            ->get();

        $products = Product::with(['datas' => function($query) {
            $query->select('name', 'city_id', 'product_id', 'count', 'price')
                ->join('cities', 'city_id', '=', 'id')
                ->orderBy('name', 'DESC');
        }])->orderBy('id', 'asc')->paginate(50);

        return view('products', ['products' => $products, 'cities' => $cities]);
    }
}
