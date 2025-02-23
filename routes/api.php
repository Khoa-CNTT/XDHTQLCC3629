<?php

use App\Http\Controllers\NhaSanXuatController;
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

//admin
Route::group(['prefix'  =>  '/admin'], function () {
    Route::group(['prefix'  =>  '/nha-san-xuat'], function () {
        Route::get('/lay-du-lieu', [NhaSanXuatController::class, 'getData']);
        // Route::post('/tim-tinh-thanh', [NhaSanXuatController::class, 'searchTinhThanh']);
        // Route::post('/them-moi-tinh-thanh', [NhaSanXuatController::class, 'createTinhThanh']);
        // Route::delete('/xoa-tinh-thanh/{id}', [NhaSanXuatController::class, 'deleteTinhThanh']);
        // Route::post('/cap-nhat-tinh-thanh', [NhaSanXuatController::class, 'updateTinhThanh']);
        // Route::post('/doi-tinh-trang-tinh-thanh', [NhaSanXuatController::class, 'doiTinhTrangTinhThanh']);
    });
});
