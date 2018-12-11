<?php

namespace App\Http\Controllers\Api\V1;

use App\ProductPart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductPartsRequest;
use App\Http\Requests\Admin\UpdateProductPartsRequest;

class ProductPartsController extends Controller
{
    public function index()
    {
        return ProductPart::all();
    }

    public function show($id)
    {
        return ProductPart::findOrFail($id);
    }

    public function update(UpdateProductPartsRequest $request, $id)
    {
        $product_part = ProductPart::findOrFail($id);
        $product_part->update($request->all());
        

        return $product_part;
    }

    public function store(StoreProductPartsRequest $request)
    {
        $product_part = ProductPart::create($request->all());
        

        return $product_part;
    }

    public function destroy($id)
    {
        $product_part = ProductPart::findOrFail($id);
        $product_part->delete();
        return '';
    }
}
