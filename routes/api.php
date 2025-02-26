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
    Route::post('/check', [NhanVienController::class, 'check']);
    Route::get('/dang-xuat', [NhanVienController::class, 'logout']);
    Route::get('/dang-xuat-tat-ca', [NhanVienController::class, 'logoutAll']);
    Route::get('/kiem-tra-token', [NhanVienController::class, 'checkToken']);
});

//admin
Route::group(['prefix'  =>  '/admin'], function () {
    Route::group(['prefix'  =>  '/nha-san-xuat'], function () {
        Route::get('/lay-du-lieu', [NhaSanXuatController::class, 'getData']);
        Route::post('/tim-nha-san-xuat', [NhaSanXuatController::class, 'searchNhaSanXuat']);
        Route::post('/them-moi-nha-san-xuat', [NhaSanXuatController::class, 'createNhaSanXuat']);
        Route::delete('/xoa-nha-san-xuat/{id}', [NhaSanXuatController::class, 'deleteNhaSanXuat']);
        Route::post('/cap-nhat-nha-san-xuat', [NhaSanXuatController::class, 'updateNhaSanXuat']);
        Route::post('/doi-tinh-trang-nha-san-xuat', [NhaSanXuatController::class, 'doiTinhTrangNhaSanXuat']);
    });
});
