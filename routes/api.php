<?php

use App\Http\Controllers\BlockChainController;
use App\Http\Controllers\ChiTietSanPhamController;
use App\Http\Controllers\DaiLyController;
use App\Http\Controllers\DanhMucSanPhamController;
use App\Http\Controllers\DonViVanChuyenController;
use App\Http\Controllers\DonHangController;
use App\Http\Controllers\GiaoDichController;
use App\Http\Controllers\GioHangController;
use App\Http\Controllers\NguyenLieuController;
use App\Http\Controllers\NguyenLieuSanPhamController;
use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\NhaSanXuatController;
use App\Http\Controllers\PhuongTienController;
use App\Http\Controllers\QuanHuyenController;
use App\Http\Controllers\SanPhamController;
use App\Http\Controllers\SanPhamNSXController;
use App\Http\Controllers\TinhThanhController;
use Illuminate\Support\Facades\Route;

Route::post("/check-giao-dich", [GiaoDichController::class, 'checkPaid']);
Route::get('/check-nguoi-dung', [NhanVienController::class, 'checkNguoiDung']);

//auth user
Route::group(['prefix'  =>  '/auth'], function () {
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
    Route::group(['prefix'  =>  '/tinh-thanh'], function () {
        Route::get('/lay-du-lieu-tinh', [TinhThanhController::class, 'getData']);
    });
    Route::group(['prefix'  =>  '/quan-huyen'], function () {
        Route::get('/lay-du-lieu-qh', [QuanHuyenController::class, 'getData']);
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
    });

    Route::group(['prefix'  =>  '/nguyen-lieu'], function () {
        Route::get('/lay-du-lieu', [NguyenLieuController::class, 'getDataforAdmin']);
        Route::post('/doi-tinh-trang', [NguyenLieuController::class, 'changeTrangthaiAdmin']);
        Route::post('/tim-nguyen-lieu', [NguyenLieuController::class, 'searchNguyenLieuAdmin']);
        Route::delete('/xoa-nguyen-lieu/{id}', [NguyenLieuController::class, 'deleteNguyenLieu']);
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
    Route::group(['prefix'  =>  '/don-vi-van-chuyen'], function () {
        Route::get('/lay-du-lieu', [DonViVanChuyenController::class, 'getData']);
        Route::post('/them-moi-don-vi-van-chuyen', [DonViVanChuyenController::class, 'createDVVC']);
        Route::post('/cap-nhat-don-vi-van-chuyen', [DonViVanChuyenController::class, 'updateDVVC']);
        Route::post('/tim-don-vi-van-chuyen', [DonViVanChuyenController::class, 'searchDVVC']);
        Route::delete('/xoa-don-vi-van-chuyen/{id}', [DonViVanChuyenController::class, 'deleteDVVC']);
        Route::post('/doi-tinh-trang-don-vi-van-chuyen', [DonViVanChuyenController::class, 'doiTinhTrangDVVC']);
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

    Route::group(['prefix'  =>  '/don-hang'], function () {
        Route::get('/lay-du-lieu', [DonHangController::class, 'getDataForAdmin']);
        Route::post('/huy-don-hang', [DonHangController::class, 'huyDonHangAdmin']);
        Route::post('/xac-nhan-don-hang', [DonHangController::class, 'xacNhanDonHangAdmin']);
        Route::post('/chi-tiet', [DonHangController::class, 'getDataChiTietAdmin']);
        Route::post('/tim-don-hang', [DonHangController::class, 'searchDonHangAdmin']);
        Route::post('/xem-hoa-don-giao-dich', [GiaoDichController::class, 'getDataChiTietHoaDonGiaoDich']);
    });
});
//user
Route::group(['prefix'  =>  '/user', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['prefix'  =>  '/gio-hang'], function () {
        Route::post('/them-vao-gio-hang', [GioHangController::class, 'themVaoGioHang']);
        Route::get('/lay-du-lieu', [GioHangController::class, 'getData']);
        Route::post('/cap-nhat-so-luong', [GioHangController::class, 'capNhatSoLuong']);
        Route::post('/xoa-san-pham', [GioHangController::class, 'xoaSanPham']);
        Route::post('/dat-hang', [GioHangController::class, 'datHang']);
    });
    Route::group(['prefix'  =>  '/san-pham'], function () {
        Route::get('/get-data-by-user', [SanPhamController::class, 'getDataByUser']);
        Route::post('/lay-du-lieu-san-pham/data', [SanPhamController::class, 'getDataByIDSanPham']);
        Route::post('/them-moi-san-pham-cua-nsx', [SanPhamController::class, 'createSanPhamCuaNSX']);
        Route::post('/update-san-pham-cua-nsx', [SanPhamController::class, 'updateSanPhamCuaNSX']);
        Route::delete('/xoa-san-pham-cua-nsx/{id}', [SanPhamController::class, 'deleteSanPhamCuaNSX']);
        Route::post('/tim-san-pham-nha-san-xuat', [SanPhamController::class, 'searchSanPhamNSX']);
        Route::post('/doi-tinh-trang-san-pham-cua-nsx', [SanPhamController::class, 'doiTinhTrangSanPhamCuaNSX']);
    });
    Route::group(['prefix'  =>  '/nguyen-lieu'], function () {
        Route::get('/get-data-nglieu-by-user', [NguyenLieuController::class, 'getDataNgLieuByUser']);
        Route::post('/doi-tinh-trang', [NguyenLieuController::class, 'changeTrangthai']);
        Route::post('/them-moi-nguyen-lieu', [NguyenLieuController::class, 'createNguyenLieu']);
        Route::post('/cap-nhat-nguyen-lieu', [NguyenLieuController::class, 'updateNguyenLieu']);
        Route::delete('/xoa-nguyen-lieu/{id}', [NguyenLieuController::class, 'deleteNguyenLieu']);
        Route::post('/tim-nguyen-lieu', [NguyenLieuController::class, 'searchNguyenLieu']);
    });
    Route::group(['prefix'  =>  '/don-hang'], function () {
        Route::group(['prefix'  =>  '/dai-ly'], function () {
            Route::get('/lay-du-lieu', [DonHangController::class, 'getData']);
            Route::post('/chi-tiet', [DonHangController::class, 'getDataChiTiet']);
            Route::post('/huy-don-hang', [DonHangController::class, 'huyDonHang']);
            Route::post('/xac-nhan-don-hang', [DonHangController::class, 'xacNhanDonHangDaiLy']);
            Route::post('/lay-thong-tin-don-hang-blockchain', [DonHangController::class, 'getDataOrderOnBlockChain']);
            Route::post('/lay-lich-su-van-chuyen-blockchain', [DonHangController::class, 'getDataHistoryTransport']);
        });
        //nhà sản xuất
        Route::group(['prefix'  =>  '/nha-san-xuat'], function () {
            Route::post('/chi-tiet', [DonHangController::class, 'getDataChiTietForNSX']);
            Route::post('/xac-nhan-don-hang', [DonHangController::class, 'xacNhanDonHangNSX']);
            Route::post('/tim-don-hang-nsx', [DonHangController::class, 'searchDonHangNSX']);
            Route::get('/lay-du-lieu-cho-nsx', [DonHangController::class, 'getDataForNSX']);
            Route::get('/lay-du-lieu-nsx-cho-trang-chu', [DonHangController::class, 'getDataNSXchoTrangChu']);
            Route::post('/lay-thong-tin-don-hang-blockchain', [DonHangController::class, 'getDataOrderOnBlockChainForNSX']);
            Route::post('/lay-lich-su-van-chuyen-blockchain', [DonHangController::class, 'getDataHistoryTransportForNSX']);
            Route::post('/huy-don-hang', [DonHangController::class, 'huyDonHangNSX']);
        });
        //đơn vị vận chuyển
        Route::group(['prefix'  =>  '/don-vi-van-chuyen'], function () {
            Route::get('/lay-du-lieu-cho-dvvc', [DonHangController::class, 'getDataForDVVC']);
            Route::post('/xac-nhan-don-hang', [DonHangController::class, 'xacNhanDonHangDVVC']);
            Route::post('/chi-tiet', [DonHangController::class, 'getDataChiTietForDVVC']);
            Route::post('/tim-kiem-dvvc', [DonHangController::class, 'searchDonHangDVVC']);
            Route::post('/goi-y-duong-di', [DonHangController::class, 'goiYDuongDi']);
            Route::post('/lay-lich-trinh-don-hang', [DonHangController::class, 'getLichTrinh']);
            Route::post('/xac-nhan-da-den', [DonHangController::class, 'xacNhanDen']);
            Route::post('/xac-nhan-da-di', [DonHangController::class, 'xacNhanDi']);
            Route::post('/dvvc-mint', [BlockChainController::class, 'mint']);
            Route::post('/lay-thong-tin-don-hang-blockchain', [DonHangController::class, 'getDataOrderOnBlockChainForDVVC']);
        });
    });
    Route::group(['prefix'  =>  '/don-vi-van-chuyen'], function () {
        Route::get('/lay-du-lieu', [DonViVanChuyenController::class, 'getDataForDaiLy']);
    });
});
