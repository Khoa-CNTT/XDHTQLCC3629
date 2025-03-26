<?php

use App\Http\Controllers\ChiTietSanPhamController;
use App\Http\Controllers\DaiLyController;
use App\Http\Controllers\DanhMucSanPhamController;
use App\Http\Controllers\NguyenLieuController;
use App\Http\Controllers\NguyenLieuSanPhamController;
use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\NhaSanXuatController;
use App\Http\Controllers\PhuongTienController;
use App\Http\Controllers\SanPhamController;
use App\Http\Controllers\SanPhamNSXController;
use App\Models\DanhMucSanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/check-nguoi-dung', [NhanVienController::class, 'checkNguoiDung']);
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
    Route::group(['prefix'  =>  '/nhan-vien'], function () {
        Route::get('/lay-du-lieu', [NhanVienController::class, 'getData']);
        Route::post('/them-moi-nhan-vien', [NhanVienController::class, 'createNhanVien']);
        Route::post('/cap-nhat-nhan-vien', [NhanVienController::class, 'updateNhanVien']);
        Route::post('/tim-nhan-vien', [NhanVienController::class, 'searchNhanVien']);
        Route::delete('/xoa-nhan-vien/{id}', [NhanVienController::class, 'deleteNhanVien']);
        Route::post('/doi-tinh-trang-nhan-vien', [NhanVienController::class, 'doiTinhTrangNhanVien']);
    });
    Route::group(['prefix'  =>  '/san-pham'], function () {
        Route::get('/lay-du-lieu', [SanPhamController::class, 'getData']);
        Route::post('/them-moi-san-pham', [SanPhamController::class, 'createSanPham']);
        Route::post('/cap-nhat-san-pham', [SanPhamController::class, 'updateSanPham']);
        Route::post('/tim-san-pham', [SanPhamController::class, 'searchSanPham']);
        Route::delete('/xoa-san-pham/{id}', [SanPhamController::class, 'deleteSanPham']);
        Route::post('/doi-tinh-trang-san-pham', [SanPhamController::class, 'doiTinhTrangSanPham']);
        Route::get('/get-data-by-id', [SanPhamController::class, 'getDataByID']);
    });

    Route::group(['prefix'  =>  '/nguyen-lieu'], function () {
        Route::get('/lay-du-lieu', [NguyenLieuController::class, 'getData']);
        Route::post('/doi-tinh-trang', [NguyenLieuController::class, 'changeTrangthai']);
        Route::post('/them-moi-nguyen-lieu', [NguyenLieuController::class, 'createNguyenLieu']);
        Route::post('/cap-nhat-nguyen-lieu', [NguyenLieuController::class, 'updateNguyenLieu']);
        Route::delete('/xoa-nguyen-lieu/{id}', [NguyenLieuController::class, 'deleteNguyenLieu']);
        Route::post('/tim-nguyen-lieu', [NguyenLieuController::class, 'searchNguyenLieu']);
    });
    Route::group(['prefix'  =>  '/nguyen-lieu-san-pham'], function () {
        Route::get('/lay-du-lieu', [NguyenLieuSanPhamController::class, 'getData']);
        Route::get('/lay-du-lieu-nguyen-lieu-san-pham', [NguyenLieuSanPhamController::class, 'getDataDanhMuc']);
        Route::post('/them-moi-nguyen-lieu-san-pham', [NguyenLieuSanPhamController::class, 'createNguyenLieuSanPham']);
        Route::post('/cap-nhat-nguyen-lieu-san-pham', [NguyenLieuSanPhamController::class, 'updateNguyenLieuSanPham']);
        Route::delete('/xoa-nguyen-lieu-san-pham/{id}', [NguyenLieuSanPhamController::class, 'deleteNguyenLieuSanPham']);
        Route::post('/doi-tinh-trang', [NguyenLieuSanPhamController::class, 'changeTrangthai']);
        Route::post('/tim-nguyen-lieu-san-pham', [NguyenLieuSanPhamController::class, 'searchNguyenLieuSanPham']);
    });
    Route::group(['prefix'  =>  '/phuong-tien'], function () {
        Route::get('/lay-du-lieu', [PhuongTienController::class, 'getData']);
        Route::post('/them-moi-phuong-tien', [PhuongTienController::class, 'createPhuongTien']);
        Route::post('/cap-nhat-phuong-tien', [PhuongTienController::class, 'updatePhuongTien']);
        Route::post('/tim-phuong-tien', [PhuongTienController::class, 'searchPhuongTien']);
        Route::delete('/xoa-phuong-tien/{id}', [PhuongTienController::class, 'deletePhuongTien']);
        Route::post('/doi-tinh-trang-phuong-tien', [PhuongTienController::class, 'doiTinhTrangPhuongTien']);
    });

    Route::group(['prefix'  =>  '/san-pham-nsx'], function () {
        Route::get('/lay-du-lieu', [SanPhamNSXController::class, 'getData']);
        Route::post('/them-moi-san-pham-nsx', [SanPhamNSXController::class, 'createSanPhamNSX']);
        Route::post('/cap-nhat-san-pham-nsx', [SanPhamNSXController::class, 'updateSanPhamNSX']);
        Route::post('/tim-san-pham-nsx', [SanPhamNSXController::class, 'searchSanPhamNSX']);
        Route::delete('/xoa-san-pham-nsx/{id}', [SanPhamNSXController::class, 'deleteSanPhamNSX']);
        Route::post('/doi-tinh-trang-san-pham-nsx', [SanPhamNSXController::class, 'doiTinhTrangSanPhamNSX']);
    });
    Route::group(['prefix'  =>  '/chi-tiet-san-pham'], function () {
        Route::get('/lay-du-lieu', [ChiTietSanPhamController::class, 'getData']);
        Route::post('/them-moi-chi-tiet-san-pham', [ChiTietSanPhamController::class, 'createChiTietSP']);
        Route::post('/cap-nhat-chi-tiet-san-pham', [ChiTietSanPhamController::class, 'updateChiTietSP']);
        Route::post('/tim-chi-tiet-san-pham', [ChiTietSanPhamController::class, 'searchChiTietSP']);
        Route::delete('/xoa-chi-tiet-san-pham/{id}', [ChiTietSanPhamController::class, 'deleteChiTietSP']);
        Route::post('/doi-tinh-trang-chi-tiet-san-pham', [ChiTietSanPhamController::class, 'doiTinhTrangChiTietSP']);
    });
});
