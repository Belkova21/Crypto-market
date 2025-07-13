<?php

use App\Http\Controllers\CurrencyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('currencies/total', [CurrencyController::class, 'total']);
Route::apiResource('currencies', CurrencyController::class);

