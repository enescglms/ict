<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Products;
use Illuminate\Http\Request;
use App\Http\Resources\ProductListResource;

class ProductController extends Controller
{

    public function get(int $productId)
    {
        return ProductListResource::make(
            Products::findOrFail($productId)
        );
    }

    public function store(ProductRequest $request)
    {
        $productCount = Products::whereRaw(
            "LOWER(TRANSLATE(products.name, 'çÇğĞıİöÖüÜşŞ', 'cCgGiIoOuUsS')) = ?",
            $this->clearTurkishLetters($request->get('name'))
        )->count();

        if ($productCount > 0) {
            return 'Aynı isimde ürün kaydedilemez.';
        } else {
            $product = Products::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla oluşturuldu.',
                'product' => ProductListResource::make($product),
            ]);
        }
    }


    public function update(ProductRequest $request, string $id)
    {
        $product = Products::withTrashed()
            ->where('id', $id)
            ->firstOrFail();

        try {
            $product->update($request->validated());

            if ($product->trashed()) {
                return response()->json([
                    'message' => 'Ürün başarıyla güncellendi. Bu ürün silinmiş bir üründü.',
                ], 200);
            }

            return response()->json([
                'message' => 'Ürün başarıyla güncellendi.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'Hata'
            ], 500);
        }
    }


    public function destroy(string $id)
    {
        try {
            $product = Products::findOrFail($id);
            $product->delete();

            return response()->json([
                'message' => 'Ürün başarıyla silindi.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'Hata'
            ], 500);
        }
    }

    private function clearTurkishLetters(string $str)
    {
        $before = array('ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ö', 'Ç');
        $after = array('i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 'o', 'c');

        $clean = str_replace($before, $after, $str);
        $clean = preg_replace('/[^a-zA-Z0-9 ]/', '', $clean);
        return strtolower(trim($clean, '-'));
    }
}
