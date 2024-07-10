<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // FIXME
        /* İstenilen dönüş değeri;
            Siparişe ait bilgiler, durum bilgisi ile birlikte order altında,
            Müşteri isim/soyisim customer altında
            Siparişteki ürünlerin isimleri ve ID'leri products altında. Bir ürün siparişten sonra ürün tablosunda pasife alınmış olsa dahi bu endpointte listelenmelidir
         */

        return [
            'order' => [
                'id' => $this->id,
                'order_no' => $this->order_no,
                'order_date' => $this->order_date,
                'status' => $this->whenLoaded('status', function () {
                    return $this->status->status;
                })
            ],
            'customer' => $this->whenLoaded('customer', function () {
                return [
                    'name' => $this->customer->name,
                    'surname' => $this->customer->surname,
                ];
            }),
            'products' => $this->whenLoaded('products', function () {
                // Bir ürün siparişten sonra ürün tablosunda pasife alınmış olsa dahi bu endpointte listelenmelidir.
                // Burada tam olarak ne istendiğini anlayamadım? Ürün tablosunda is_active gibi bir sütun mevcut değil.
                // stock_status sütunundan bahsediliyorsa değeri 0 olsa dahi kayıtlarda gelecektir.
                // Eğer "silinmiş" bir kayıttan bahsediliyorsada ->withTrashed() ile ayarlanmalı.
                /*
                $this->products->load(['product' => function ($query) {
                    $query->withTrashed();
                }])
                */

                $data = [];
                $this->products->map(function ($product) use (&$data) {
                    $data[] = [
                        'id' => $product->product->id,
                        'name' => $product->product->name,
                    ];
                });
                return $data;
            }),
        ];
    }
}
