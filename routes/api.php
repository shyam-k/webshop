<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrdersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/
Route::prefix('orders')
    ->name('orders.')
    ->group(function () {
        Route::get('/', [OrdersController::class, 'index'])->name('index');
        Route::post('/', [OrdersController::class, 'store'])->name('store');
        Route::get('/{orders}', [OrdersController::class, 'show'])->name('show');
        Route::put('/{orders}', [OrdersController::class, 'update'])->name('update');
        Route::delete('/{orders}', [OrdersController::class, 'destroy'])->name('destroy');

        Route::put('/{orders}/add', [OrdersController::class, 'add'])->name('add');
        Route::get('/{orders}/pay', [OrdersController::class, 'pay'])->name('pay');

    });


//Route::apiResource('orders', OrdersController::class);
/*
Route::post('orders', [OrdersController::class, 'store'])->name('orders.store');
*/