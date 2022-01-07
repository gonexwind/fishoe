<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $name = $request->input('name');
        $description = $request->input('description');
        $tags = $request->input('tags');
        $categories = $request->input('categories');
        $price_form = $request->input('price_form');
        $price_to = $request->input('price_to');

        if ($id) {
            $product = Product::with([
                'category',
                'galleries'
            ])->find($id);

            if ($product) {
                return ResponseFormatter::success(
                    $product,
                    'Success get all products'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Error get all products',
                    400
                );
            }
        }

        $product = Product::with(['category', 'galleries']);
        if ($name) {
            $product->where('name', 'like', '%' . $name . '%');
        }
        if ($description) {
            $product->where('description', 'like', '%' . $description . '%');
        }
        if ($tags) {
            $product->where('tags', 'like', '%' . $tags . '%');
        }
        if ($price_form) {
            $product->where('price', '>=', $price_form);
        }
        if ($price_to) {
            $product->where('price', '<=', $price_to);
        }
        if ($categories) {
            $product->where('categories', $categories);
        }

        return ResponseFormatter::success(
            $product->paginate($limit),
            'Success get products',
        );
    }
}