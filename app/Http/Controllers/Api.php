<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderListRequest;
use App\Http\Resources\OrderListResource;
use App\Models\Orders;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class Api
{
    public function orders(OrderListRequest $request)
    {
        try {
            $data = OrderListResource::collection(
                Orders::with([
                    'products',
                    'customer',
                    'status',
                ])
                ->where('customer_id', $request->get('customer_id'))
                ->when($request->has('order_no'), function ($query) use ($request) {
                    $query->where('order_no', $request->get('order_no'));
                })
                ->orderBy('id', 'DESC')
                ->get()
            );

            return response()->json([
                'orders' => $data->resolve(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => 'Hata'
            ], 500);
        }
    }
}
