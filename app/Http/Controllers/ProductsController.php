<?php

namespace App\Http\Controllers;


use App\Components\Response;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductsController extends Controller
{
    /**
     * ProductsController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['index', 'show']
        ]);
        $this->middleware('admin', [
            'except' => ['index', 'show']
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return Response::success(Product::all(), 'Successfully fetched all products');
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, int $id)
    {
        $product = Product::with('price')->find($id);

        if (!is_null($product)) {
            return Response::success($product, 'Successfully fetched a single product');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'sku' => 'required|alpha_num|unique:products',
            'description' => 'required',
            'qty' => 'required|numeric',
            'price' => 'required|numeric',
            'product_type_id' => ['required', Rule::in([1,2])]

        ]);

        $price_attributes = ['price', 'discount', 'discount_percentage'];
        $product_attributes = ['name', 'description', 'sku', 'qty', 'product_type_id'];

        $product_data = $request->only($product_attributes);

        $product = Product::create($product_data);

        $price_data = [];

        foreach ($price_attributes as $attribute) {
            if (array_key_exists($attribute, $request->all())) {
                $price_data[$attribute] = $request->get($attribute);
            }
        }

        //Create product price entry
        $product->price()->create($price_data);

        return Response::success($product, 'product successfully created');
    }

}
