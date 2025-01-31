<?php
//fonte: https://techsolutionstuff-com.translate.goog/post/how-to-publish-api-route-file-in-laravel-11?_x_tr_sl=en&_x_tr_tl=pt&_x_tr_hl=pt&_x_tr_pto=wa
// php artisan install:api

use App\Http\Controllers\Api\QuoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/quote', [QuoteController::class, 'getRandomQuote']);
