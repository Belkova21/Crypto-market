<?php

namespace App\Services;

use App\Http\Resources\CurrencyCollection;
use App\Http\Resources\CurrencyDetailResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CoinGeckoService
{
    protected string $baseUrl = 'https://api.coingecko.com/api/v3';

    /**
     * Fetch paginated list of currencies with market data.
     */
    public function fetchCurrencies(
        int $page = 1,
        int $perPage = 25,
        string $sortBy = 'market_cap',
        string $sortOrder = 'desc'
    ): CurrencyCollection|array
    {
        try {
            $response = Http::get("{$this->baseUrl}/coins/markets", [
                'vs_currency' => 'usd',
                'sparkline' => 'true',
                'price_change_percentage' => '1h,24h,7d',
                'per_page' => $perPage,
                'page' => $page,
                'order' => "{$sortBy}_{$sortOrder}",
            ]);

            if ($response->successful()) {
                return new CurrencyCollection($response->json());
            }

            Log::error('Failed to fetch data from CoinGecko', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }catch (Exception $exception){
            Log::error('Exception while fetching currencies from CoinGecko', [
                'message' => $exception->getMessage(),
            ]);
        }

        return new CurrencyCollection([]);
    }

    /**
     * Fetch detailed data and chart for a single currency.
     */
    public function fetchCurrencyById(string $id): CurrencyDetailResource|JsonResponse
    {
        try {
//            $detailResponse = Http::get("{$this->baseUrl}/coins/{$id}");
            $chartResponse = Http::get("{$this->baseUrl}/coins/{$id}/market_chart", [
                'vs_currency' => 'usd',
                'days' => 7,
                'interval' => 'daily',
            ]);

            if (!$chartResponse->successful()) {
                Log::warning("CoinGecko detail/chart fetch failed", [
                    'id' => $id,
                    'chart_status' => $chartResponse->status(),
                ]);

                return response()->json(['error' => 'Failed to fetch data from CoinGecko'], 500);
            }

            return new CurrencyDetailResource([
//                'coin' => $detailResponse->json(),
                'chart' => $chartResponse->json(),
            ]);
        } catch (Exception $exception){
            Log::error('Exception while fetching coin details from CoinGecko', [
                'id' => $id,
                'message' => $exception->getMessage(),
            ]);

            return response()->json(['error' => 'Exception occurred while fetching data.'], 500);
        }
    }

}
