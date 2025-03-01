<?php

use App\Http\Controllers\DaiLyController;
use App\Http\Controllers\DanhMucSanPhamController;
use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\NhaSanXuatController;
use App\Models\DanhMucSanPham;
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
    Route::group(['prefix'  =>  '/dai-ly'], function () {
        Route::get('/lay-du-lieu', [DaiLyController::class, 'getData']);
        Route::post('/tim-dai-ly', [DaiLyController::class, 'searchDaiLy']);
        Route::post('/them-moi-dai-ly', [DaiLyController::class, 'createDaiLy']);
        Route::delete('/xoa-dai-ly/{id}', [DaiLyController::class, 'deleteDaiLy']);
        Route::post('/cap-nhat-dai-ly', [DaiLyController::class, 'updateDaiLy']);
        Route::post('/doi-tinh-trang-dai-ly', [DaiLyController::class, 'doiTinhTrangDaiLy']);
    });
    Route::group(['prefix'  =>  '/danh-muc-sp'], function () {
        Route::get('/lay-du-lieu', [DanhMucSanPhamController::class, 'getData']);
        Route::post('/tim-danh-muc', [DanhMucSanPhamController::class, 'searchDanhMuc']);
        Route::post('/them-moi-danh-muc', [DanhMucSanPhamController::class, 'createDanhMuc']);
        Route::delete('/xoa-danh-muc/{id}', [DanhMucSanPhamController::class, 'deleteDanhMuc']);
        Route::post('/cap-nhat-danh-muc', [DanhMucSanPhamController::class, 'updateDanhMuc']);
        Route::post('/doi-tinh-trang-danh-muc', [DanhMucSanPhamController::class, 'doiTinhTrangDanhMuc']);
    });
});
Route::group(['prefix'  =>  '/admin'], function () {
    Route::group(['prefix'  =>  '/nhan-vien'], function () {
        Route::get('/lay-du-lieu', [NhanVienController::class, 'getData']);
        Route::post('/them-moi-nhan-vien', [NhanVienController::class, 'createNhanVien']);
        Route::post('/cap-nhat-nhan-vien', [NhanVienController::class, 'updateNhanVien']);
        Route::post('/tim-nhan-vien', [NhanVienController::class, 'searchNhanVien']);
        Route::delete('/xoa-nhan-vien/{id}', [NhanVienController::class, 'deleteNhanVien']);
        Route::post('/doi-tinh-trang-nhan-vien', [NhanVienController::class, 'doiTinhTrangNhanVien']);
    });
});
