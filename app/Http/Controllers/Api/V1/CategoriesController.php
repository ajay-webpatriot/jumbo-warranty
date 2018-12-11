<?php

namespace App\Http\Controllers\Api\V1;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoriesRequest;
use App\Http\Requests\Admin\UpdateCategoriesRequest;

class CategoriesController extends Controller
{
    public function index()
    {
        return Category::all();
    }

    public function show($id)
    {
        return Category::findOrFail($id);
    }

    public function update(UpdateCategoriesRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->all());
        
        $products           = $category->products;
        $currentProductData = [];
        foreach ($request->input('products', []) as $index => $data) {
            if (is_integer($index)) {
                $category->products()->create($data);
            } else {
                $id                          = explode('-', $index)[1];
                $currentProductData[$id] = $data;
            }
        }
        foreach ($products as $item) {
            if (isset($currentProductData[$item->id])) {
                $item->update($currentProductData[$item->id]);
            } else {
                $item->delete();
            }
        }

        return $category;
    }

    public function store(StoreCategoriesRequest $request)
    {
        $category = Category::create($request->all());
        
        foreach ($request->input('products', []) as $data) {
            $category->products()->create($data);
        }

        return $category;
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return '';
    }
}
