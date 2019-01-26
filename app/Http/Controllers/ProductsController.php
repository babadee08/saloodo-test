<?php

namespace App\Http\Controllers;


use App\Components\Response;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['index', 'show']
        ]);
    }

    //getAllProduct
    public function index()
    {
        return Response::success(Product::all(), 'Successfully fetched all products');
    }

    public function show(Request $request, int $id)
    {
        $product = Product::with('price')->find($id);

        if (!is_null($product)) {
            return Response::success($product, 'Successfully fetched a single product');
        }
    }
}
