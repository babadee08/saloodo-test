<?php

namespace App\Http\Controllers;


use App\Components\Response;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    //getAllProduct
    public function index()
    {
        return Response::success(Product::all(), 'Successfully fetched all products');
    }
}
