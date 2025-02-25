<?php

use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\NhaSanXuatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//auth admin
Route::group(['prefix'  =>  '/auth-admin'], function () {
    Route::post('/login', [NhanVienController::class, 'login']);
    Route::post('/register', [NhanVienController::class, 'register']);
    Route::post('/check', [NhanVienController::class, 'check']);
    Route::get('/dang-xuat', [NhanVienController::class, 'logout']);
    Route::get('/dang-xuat-tat-ca', [NhanVienController::class, 'logoutAll']);
    Route::get('/kiem-tra-token-client', [NhanVienController::class, 'checkToken']);
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
