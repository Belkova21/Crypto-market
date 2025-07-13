<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Jobs\TrackCurrencyUpdates;

class TrackCachedCurrencyUpdates extends Command
{
    protected $signature = 'currencies:track';
    protected $description = 'Fetch and broadcast currency updates using cached params';

    public function handle(): int
    {
        $params = Cache::get('currency_tracking_params');
        if (!$params) {
            return Command::SUCCESS;
        }

        $page = $params['page'] ?? 1;
        $perPage = $params['perPage'] ?? 10;
        $sortBy = $params['sortBy'] ?? 'market_cap';
        $sortOrder = $params['sortOrder'] ?? 'desc';
        $data = $params['data'] ?? [];

        TrackCurrencyUpdates::dispatch($data, $page, $perPage, $sortBy, $sortOrder);

        $this->info("Currency update dispatched for page=$page, perPage=$perPage, sortBy=$sortBy, sortOrder=$sortOrder.");

        return Command::SUCCESS;
    }
}
