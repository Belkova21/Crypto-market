<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
//            'id' => $this['coin']['id'],
//            'name' => $this['coin']['name'],
//            'symbol' => $this['coin']['symbol'],
//            'current_price' => $this['coin']['market_data']['current_price']['usd'],
//            'price_change_7d' => $this['coin']['market_data']['price_change_percentage_7d_in_currency']['usd'],
//            'market_cap_change_24h' => $this['coin']['market_data']['market_cap_change_percentage_24h'],
//            'total_volume' => $this['coin']['market_data']['total_volume']['usd'],
//            'fvd' => $this['coin']['market_data']['fully_diluted_valuation']['usd'],
//            'total_supply' => $this['coin']['market_data']['circulating_supply'],
            'chart_price' => $this['chart']['prices'],
            'chart_market_cap' => $this['chart']['market_caps'],

        ];
    }
}
