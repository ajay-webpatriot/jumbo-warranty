<?php

namespace App\Http\Controllers\Api\V1;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductsRequest;
use App\Http\Requests\Admin\UpdateProductsRequest;

class ProductsController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function show($id)
    {
        return Product::findOrFail($id);
    }

    public function update(UpdateProductsRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->all());
        

        return $product;
    }

    public function store(StoreProductsRequest $request)
    {
        $product = Product::create($request->all());
        

        return $product;
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return '';
    }
}
