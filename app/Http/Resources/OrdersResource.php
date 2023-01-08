<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrdersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customer' => $this->customer,
            'payed' => $this->payed,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'products' => $this->products,
            'total' => number_format(($this->products->sum('price')),2)
        ];
    }
}
