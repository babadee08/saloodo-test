<?php

namespace App\Http\Controllers;


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
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully fetched all products',
            'data' => Product::all()
        ]);
    }
}
