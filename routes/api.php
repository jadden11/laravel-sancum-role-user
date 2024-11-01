<?php

use App\Http\Controllers\authController;
use App\Http\Controllers\ProductController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function () {
    return response()->json([
        'status' => false,
        'message' => 'Access is not allowed',
        'developer' => 'Moh Reza'
    ], 401);
})->name('login');


Route::get('/products', [ProductController::class, 'index'])->middleware('auth:sanctum');

Route::post('/register', [authController::class, 'register']);
Route::post('/login', [authController::class, 'login']);
