<?php

namespace App\Http\Controllers;

use App\Http\Resources\CurrencyCollection;
use App\Http\Resources\CurrencyDetailResource;
use App\Jobs\TrackCurrencyUpdates;
use App\Services\CoinGeckoService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): CurrencyCollection|JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 25);
        $sortBy = $request->input('sort_by', 'market_cap');
        $sortOrder = $request->input('sort_order', 'desc');

        try {
            $data = (new CoinGeckoService())->fetchCurrencies($page, $perPage, $sortBy, $sortOrder);
            $cacheData = $data->resolve();

            Cache::put('currency_tracking_params', compact('page', 'perPage', 'sortBy', 'sortOrder', 'cacheData'));

            try {
                TrackCurrencyUpdates::dispatch($cacheData, $page, $perPage, $sortBy, $sortOrder);
                Log::info('Dispatched TrackCurrencyUpdates job.', ['page' => $page, 'perPage' => $perPage]);
            } catch (Exception $e) {
                Log::error('Failed to dispatch WebSocket update job.', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Failed to dispatch update job.'], 500);
            }

            return $data;
        } catch (Exception $e) {
            Log::error('Failed to fetch currencies from CoinGecko.', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch currency data.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): CurrencyDetailResource|JsonResponse
    {
        try {
            return (new CoinGeckoService())->fetchCurrencyById($id);
        } catch (Exception $e) {
            Log::error("Failed to fetch currency detail for ID: $id", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Currency not found or failed to fetch.'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Total number of currencies.
     */
    public function total(): JsonResponse
    {
        try {
            $response = Http::get('https://api.coingecko.com/api/v3/coins/list');

            if ($response->successful()) {
                $currencies = $response->json();
                return response()->json(['total' =>  count($currencies)]);
            } else {
                Log::error("CoinGecko response failed", ['body' => $response->body()]);
                return response()->json(['error' => 'Failed to fetch from CoinGecko'], 500);
            }
        } catch (Exception $e) {
            Log::error("Exception in total()", ['message' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
