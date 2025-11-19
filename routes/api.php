<?php

use App\Http\Controllers\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('throttle:30,1')->group(function () {
  Route::get('/events/discover', [EventController::class, 'index'])
    ->name('api.events.discover');
  Route::get('/events/discover/policy/{policyHash}', [EventController::class, 'byPolicy'])
    ->name('api.events.by-policy');
})->middleware('cache.headers:public;max_age=60;etag');

Route::middleware('auth:sanctum')
  ->get('/user', static function (Request $request) {
    return $request->user();
  });
