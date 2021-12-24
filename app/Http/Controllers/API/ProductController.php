<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Models\Product;

class ProductController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $productName = $request->input('productName');
        $description = $request->input('description');
        $tags = $request->input('tags');
        $category_id = $request->input('category_id');

        $priceFrom = $request->input('priceFrom');
        $priceTo = $request->input('priceTo');

        if ($id) {
            $product = Product::with('category', 'galleries')->find($id);

            if($product) {
                return ResponseFormatter::success($product, 'Produk berhasil dimuat!');
            } else {
                return ResponseFormatter::error(null, 'Produk tidak ada!', 404);
            }
        }

        $product = Product::with(['category', 'galleries']);

        if ($productName) {
            $product->where('name', 'like', '%'.$productName.'%');
        }

        if ($description) {
            $product->where('description', 'like', '%'.$description.'%');
        }

        if ($tags) {
            $product->where('tags', 'like', '%'.$tags.'%');
        }

        if ($priceFrom) {
            $product->where('price', '>=', $priceFrom);
        }

        if($priceTo) {
            $product->where('price', '<=', $priceTo);
        }

        if($category_id) {
            $product->where('category_id', $category_id);
        }

        return ResponseFormatter::success($product->paginate($limit), 'Produk berhasil dimuat!');
    }
}
