<?php

namespace App\Jobs;

use App\Events\CurrencyUpdated;
use App\Services\CoinGeckoService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class TrackCurrencyUpdates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//    protected string $trackingId;
    protected array $previousData;
    protected int $page;
    protected int $perPage;
    protected string $sortBy;
    protected string $sortOrder;

    public function __construct(array $initialData, int $page, int $perPage, string $sortBy, string $sortOrder)
    {
        $this->previousData = $initialData;
        $this->page = $page;
        $this->perPage = $perPage;
        $this->sortBy = $sortBy;
        $this->sortOrder = $sortOrder;
    }

    public function handle(): void
    {
        try{
            $service = new CoinGeckoService();
            $newDataResource = $service->fetchCurrencies(
                $this->page,
                $this->perPage,
                $this->sortBy,
                $this->sortOrder
            );
            $newData = $newDataResource->resolve();
            $changes = $this->detectChanges($this->previousData, $newData);

            if (!empty($changes)) {
                try {
                    broadcast(new CurrencyUpdated($changes));
                    Log::info('Broadcasted currency update.', ['count' => count($changes)]);
                } catch (Exception $e) {
                    Log::error('Failed to broadcast currency updates.', ['error' => $e->getMessage()]);
                }

                Cache::put('currency_tracking_params', [
                    'page' => $this->page,
                    'per_page' => $this->perPage,
                    'sort_by' => $this->sortBy,
                    'sort_order' => $this->sortOrder,
                    'data' => $newData,
                ]);
            }
        } catch (Exception $e) {
            Log::error('TrackCurrencyUpdates job failed.', [
                'message' => $e->getMessage(),
                'context' => [
                    'page' => $this->page,
                    'perPage' => $this->perPage,
                    'sortBy' => $this->sortBy,
                    'sortOrder' => $this->sortOrder,
                ]
            ]);
        }
    }

    protected function detectChanges(array $old, array $new): array
    {
        $diff = [];

        foreach ($new as $coin) {
            $coinId = $coin['id'] ?? null;
            if (!$coinId) continue;

            $oldCoin = collect($old)->firstWhere('id', $coinId);
            $diffCoin = [];

            foreach ($coin as $key => $value) {
                if (!is_array($value)) {
                    if (!isset($oldCoin[$key]) || $oldCoin[$key] !== $value) {
                        $diffCoin[$key] = $value;
                    }
                }
            }

            if (!empty($diffCoin)) {
                $diffCoin['id'] = $coinId;
                $diff[] = $diffCoin;
            }
        }

        return $diff;
    }
}
