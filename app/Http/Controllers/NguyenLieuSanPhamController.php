<?php

namespace App\Http\Controllers;

use App\Models\NguyenLieu;
use App\Models\NguyenLieuSanPham;
use Illuminate\Http\Request;

class NguyenLieuSanPhamController extends Controller
{
    public function getdata()
    {
        $data = NguyenLieuSanPham::join('nguyen_lieus', 'nguyen_lieu_san_phams.id_nguyen_lieu', '=', 'nguyen_lieus.id')
            ->select('nguyen_lieu_san_phams.*', 'nguyen_lieus.ten_nguyen_lieu')
            ->get();
        return response()->json([
            'status'    => true,
            'san_pham'  => $data
        ]);
    }

    public function getDataDanhMuc()
    {
        $data = NguyenLieu::all();

        return response()->json([
            'status'    => true,
            'danh_muc' => $data
        ]);
    }

    public function searchNguyenLieuSanPham(Request $request)
    {
        $key = "%" . $request->abc . "%";
        $id_nguyen_lieu = $request->id_nguyen_lieu;

        $query = NguyenLieuSanPham::join('nguyen_lieus', 'nguyen_lieu_san_phams.id_nguyen_lieu', '=', 'nguyen_lieus.id')
            ->select('nguyen_lieu_san_phams.*', 'nguyen_lieus.ten_nguyen_lieu')
            ->where('nguyen_lieu_san_phams.ma_san_pham', 'like', $key);

        // Nếu có id_nguyen_lieu, lọc thêm theo nguyên liệu
        if ($id_nguyen_lieu) {
            $query->where('nguyen_lieu_san_phams.id_nguyen_lieu', $id_nguyen_lieu);
        }

        $data = $query->get();

        return response()->json([
            'status' => true,
            'nguyen_lieu' => $data,
        ]);
    }

    public function createNguyenLieuSanPham(Request $request)
    {
        NguyenLieuSanPham::create([
            'ma_san_pham'          => $request->ma_san_pham,
            'id_nguyen_lieu'       => $request->id_nguyen_lieu,
            'so_luong_nguyen_lieu' => $request->so_luong_nguyen_lieu,
            'tinh_trang'           => $request->tinh_trang,
        ]);
        return response()->json([
            'status'  => true,
            'message' => 'Đã tạo mới sản phẩm thành công!',
        ]);
    }

    public function updateNguyenLieuSanPham(Request $request)
    {
        NguyenLieuSanPham::where('id', $request->id)
            ->update([
                'ma_san_pham'          => $request->ma_san_pham,
                'id_nguyen_lieu'       => $request->id_nguyen_lieu,
                'so_luong_nguyen_lieu' => $request->so_luong_nguyen_lieu,
                'tinh_trang'           => $request->tinh_trang,
            ]);
        return response()->json([
            'status'  => true,
            'message' => 'Đã cập nhật thành công sản phẩm ' . $request->ten_san_pham,
        ]);
    }

    public function deleteNguyenLieuSanPham($id)
    {
        $data = NguyenLieuSanPham::where('id', $id)->first();
        if ($data) {
            $data->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Đã xóa nguyên liệu thành công!',
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm được nguyên liệu để xóa!',
            ]);
        }
    }

    public function changeTrangthai(Request $request)
    {
        $tinh_trang_moi = $request->tinh_trang == 1 ? 0 : 1;
        NguyenLieuSanPham::where('id', $request->id)->update([
            'tinh_trang' => $tinh_trang_moi,
        ]);
        return response()->json([
            'status'  => true,
            'message' => 'Đã đổi trạng thái thành công',
        ]);
    }
}
