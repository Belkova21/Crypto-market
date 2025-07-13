<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);
        return [
            'id' => $this['id'],
            'symbol'=> $this['symbol'],
            'name' => $this['name'],
            'image' => $this['image'],
            'current_price' => $this['current_price'],
            'total_supply' => $this['total_supply'],
            'fvd' => $this['fully_diluted_valuation'],
            'market_cap' => $this['market_cap'],
            'volume' => $this['total_volume'],
            'price_change_percentage_24h' => $this['price_change_percentage_24h_in_currency'],
            'price_change_percentage_7d' => $this['price_change_percentage_7d_in_currency'],
            'price_change_percentage_1h' => $this['price_change_percentage_1h_in_currency'],
            'sparkline_in_7d' => $this['sparkline_in_7d']['price']
        ];
    }
}
