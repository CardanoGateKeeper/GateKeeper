<?php

  use App\Http\Controllers\CheckinController;
  use App\Http\Controllers\CheckoutController;
  use App\Http\Controllers\EventAdminController;
  use App\Http\Controllers\EventCheckinController;
  use App\Http\Controllers\EventController;
  use App\Http\Controllers\EventPolicyController;
  use App\Http\Controllers\ImageController;
  use App\Http\Controllers\TeamPolicyController;
  use App\Http\Controllers\TicketController;
  use Illuminate\Foundation\Application;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Route;
  use Inertia\Inertia;


  /*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
  */

  Route::get('/', static function () {
    return Inertia::render('Welcome', [
      'canLogin' => Route::has('login'),
      'canRegister' => Route::has('register'),
      'laravelVersion' => Application::VERSION,
      'phpVersion' => PHP_VERSION,
    ]);
  });

  Route::resource('event', EventController::class);
  Route::resource('ticket', TicketController::class);
  Route::get('image/{asset_key}', [ImageController::class, 'show'])
       ->name('image.show');

  Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
  ])
       ->group(static function () {
         Route::get('/dashboard', static function (Request $request) {
           return Inertia::render('Dashboard');
         })
              ->name('dashboard');

         Route::resource('scan-tickets', EventCheckinController::class);
         Route::resource('event.check-in', CheckinController::class);
         Route::resource('event.check-out', CheckoutController::class);
         Route::resource('manage-event', EventAdminController::class);

         Route::resource('team.policy', TeamPolicyController::class)
              ->shallow()
              ->names([
                'show' => 'team.policy.show'
              ]);
         Route::resource('event.policy', EventPolicyController::class);
       });

