<?php

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

  Route::get('/teapot', static function (Request $request) {
    exit(config('app.nftcdn_domain'));
  });

  Route::middleware('auth:sanctum')
       ->get('/user', static function (Request $request) {
         return $request->user();
       });
