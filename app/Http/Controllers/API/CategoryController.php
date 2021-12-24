<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Helpers\ResponseFormatter;

class CategoryController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $categoryName = $request->input('categoryName');
        $show_product = $request->input('show_product');

        if ($id) {
            $categories = Category::with(['products'])->find($id);

            if ($categories) {
                return ResponseFormatter::success($categories, 'Data category berhasil dimuat!');
            } else {
                return ResponseFormatter::error(null, 'Category tidak ada!', 404);
            }
        }

        $categories = Category::query();

        if($categoryName) {
            $categories->where('categoryName', $categoryName); 
        }

        if ($show_product) {
            $categories-with('products');
        }
        return ResponseFormatter::success($categories->paginate($limit), 'Data kategori berhasil dimuat!');
    }
}
