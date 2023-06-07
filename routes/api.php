<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProductsController;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::apiResource('/product', ProductsController::class);

Route::controller(AuthController::class)->group(function (){
    Route::post('login','login');
    Route::post('register','register');
});

Route::controller(ProductsController::class)->group(function(){
    Route::get('product','index');
    Route::get('product/{id}','show');
    Route::post('product','store');
    Route::put('product/{id}','update');
    Route::delete('product/{id}','destroy');
});

Route::fallback(function(){
    return response()->json(
        [
            'status'=> false,
            'message'=> 'page not found'
        ],
    404);
});
