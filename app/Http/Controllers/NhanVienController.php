<?php

namespace App\Http\Controllers;

use App\Models\DaiLy;
use App\Models\NhanVien;
use App\Models\NhaSanXuat;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Js;
use Jenssegers\Agent\Agent;

use function Laravel\Prompts\password;

class NhanVienController extends Controller
{
    public function login(Request $request)
    {
        $list       = ['nhan_vien', 'nha_san_xuat', 'dai_ly'];
        $list_token = ['api_token_nhanvien', 'api_token_nhasanxuat', 'api_token_daily'];
        $list_model = [
            NhanVien::class,
            NhaSanXuat::class,
            DaiLy::class
        ];
        for ($i=0; $i < count($list); $i++) {
            $check = Auth::guard($list[$i])->attempt([ //thử xác thực đăng nhập với từng loại tài khoản.
                'email'     => $request->email,
                'password'  => $request->password,
            ]);

            if($check) {
                $user =  Auth::guard($list[$i])->user(); //Lấy thông tin người dùng theo guard tương ứng
                if($user && $user instanceof $list_model[$i]) {
                    return response()->json([
                        'status'    => true,
                        'message'   => 'Đăng nhập thành công!',
                        'token'     => $user->createToken($list_token[$i])->plainTextToken //tạo token tương ứng
                    ]);
                }
            }
        }
        return response()->json([
            'status'  => false,
            'message' => 'Đăng nhập thất bại!'
        ]);
    }

    public function check(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message'   => 'Bạn Cần Đăng Nhập Hệ Thống',
                'status'    => false,
            ], 401);
        }

        $agent = new Agent();
        $device   = $agent->device();
        $os       = $agent->platform();
        $browser  = $agent->browser();

        $check_user = DB::table('personal_access_tokens')
            ->where('id', $user->currentAccessToken()->id)
            ->first();

        $valid_types = [
            "App\\Models\\NhanVien",
            "App\\Models\\DaiLy",
            "App\\Models\\NhaSanXuat"
        ];

        if (!in_array($check_user->tokenable_type, $valid_types)) {
            return response()->json([
                'message'   => 'Bạn Chưa Đăng Nhập',
                'status'    => false,
            ], 401);
        }

        DB::table('personal_access_tokens')
            ->where('id', $user->currentAccessToken()->id)
            ->update([
                'ip'         => request()->ip(),
                'device'     => $device,
                'os'         => $os,
                'trinh_duyet'=> $browser,
            ]);

        return response()->json([
            'email'             =>      $user->email,
            'ho_ten'            =>      $user->ho_ten ?? $user->ten_cong_ty,
            'list'              =>      $user->tokens,
            'loai_tai_khoan'    =>      $user->loai_tai_khoan,
            'so_dien_thoai'     =>      $user->so_dien_thoai,
            'dia_chi'           =>      $user->dia_chi,
            'user_id'           =>      $user->id,
        ], 200);
    }


    public function logout()
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        }

        $check_user = DB::table('personal_access_tokens')
            ->where('id', $user->currentAccessToken()->id)
            ->first();

        $valid_types = [
            "App\\Models\\NhanVien",
            "App\\Models\\DaiLy",
            "App\\Models\\NhaSanXuat"
        ];

        if (!in_array($check_user->tokenable_type, $valid_types)) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập!',
                'status'  => false,
            ], 401);
        }

        DB::table('personal_access_tokens')
            ->where('id', $user->currentAccessToken()->id)
            ->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công!',
            'status'  => true,
        ], 200);
    }

    public function logoutAll()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $tokens = $user->tokens;
            foreach ($tokens as $key => $value) {
                $value->delete();
            }

            return response()->json([
                'message'   =>  'Đã đăng xuất tất cả thành công!',
                'status'    =>  true,
            ]);
        } else {
            return response()->json([
                'message'   =>  'Bạn cần đăng nhập hệ thống',
                'status'    =>  false,
            ]);
        }
    }

    public function checkToken()
    {
        // Lấy thông tin từ Authorization: 'Bearer ' gửi lên
        $user = Auth::guard('sanctum')->user();

        if ($user instanceof \App\Models\NhanVien ||
            $user instanceof \App\Models\DaiLy ||
            $user instanceof \App\Models\NhaSanXuat) {
            return response()->json([
                'status'  => true,
                'message' => "Oke, bạn có thể đi qua",
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => "Bạn cần đăng nhập hệ thống trước",
        ]);
    }

    //admin - quản lý nhân viên
    public function getData()
    {
        // $id_chuc_nang   = ;
        // $user   =  Auth::guard('sanctum')->user();
        // $check  =   ChiTietChucNang::where('id_chuc_vu', $user->id_chuc_vu)
        //     ->where('id_chuc_nang', $id_chuc_nang)
        //     ->first();
        // if (!$check) {
        //     return response()->json([
        //         'status'    =>  false,
        //         'message'   =>  'Bạn không đủ quyền truy cập chức năng này!',
        //     ]);
        // }
        $data = NhanVien::get();

        return response()->json([
            'status'    =>  true,
            'nhan_vien' => $data
        ]);
    }

    public function createNhanVien(Request $request)
    {
        // $id_chuc_nang   = ;
        // $user   =  Auth::guard('sanctum')->user();
        // $check  =   ChiTietChucNang::where('id_chuc_vu', $user->id_chuc_vu)
        //     ->where('id_chuc_nang', $id_chuc_nang)
        //     ->first();
        // if (!$check) {
        //     return response()->json([
        //         'status'    =>  false,
        //         'message'   =>  'Bạn không đủ quyền truy cập chức năng này!',
        //     ]);
        // }
        NhanVien::create([
            'ho_ten'    =>  $request->ho_ten,
            'email'  =>  $request->email,
            'password'       =>  bcrypt($request->password),
            'id_chuc_vu' =>  $request->id_chuc_vu,
            'tinh_trang'    =>  $request->tinh_trang
        ]);
        return response()->json([
            'status'    =>  true,
            'message'   =>  'Đã tạo mới nhân viên thành công!'
        ]);
    }

    public function updateNhanVien(Request $request)
    {
        // $id_chuc_nang   = ;
        // $user   =  Auth::guard('sanctum')->user();
        // $check  =   ChiTietChucNang::where('id_chuc_vu', $user->id_chuc_vu)
        //                             ->where('id_chuc_nang', $id_chuc_nang)
        //                             ->first();
        // if (!$check) {
        //     return response()->json([
        //         'status'    =>  false,
        //         'message'   =>  'Bạn không đủ quyền truy cập chức năng này!',
        //     ]);
        // }
        try {
            $data   = $request->all();
            NhanVien::find($request->id)->update($data);
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Đã cập nhật thành công nhân viên!',
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi',
            ]);
        }
    }
    public function deleteNhanVien($id)
    {
        // $id_chuc_nang   = ;
        // $user   =  Auth::guard('sanctum')->user();
        // $check  =   ChiTietChucNang::where('id_chuc_vu', $user->id_chuc_vu)
        //     ->where('id_chuc_nang', $id_chuc_nang)
        //     ->first();
        // if (!$check) {
        //     return response()->json([
        //         'status'    =>  false,
        //         'message'   =>  'Bạn không đủ quyền truy cập chức năng này!',
        //     ]);
        // }
        try {
            NhanVien::where('id', $id)->delete();
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Xóa nhân viên thành công!',
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi xóa nhân viên',
            ]);
        }
    }
    public function searchNhanVien(Request $request)
    {
        // $id_chuc_nang   = 2;
        // $user   =  Auth::guard('sanctum')->user();
        // $check  =   ChiTietChucNang::where('id_chuc_vu', $user->id_chuc_vu)
        //     ->where('id_chuc_nang', $id_chuc_nang)
        //     ->first();
        // if (!$check) {
        //     return response()->json([
        //         'status'    =>  false,
        //         'message'   =>  'Bạn không đủ quyền truy cập chức năng này!',
        //     ]);
        // }
        $key = "%" . $request->abc . "%";

        $data   = NhanVien::where('ho_ten', 'like', $key)
            ->get();

        return response()->json([
            'status'    =>  true,
            'nhan_vien'  =>  $data,
        ]);
    }

    public function doiTinhTrangNhanVien(Request $request)
    {
        // $id_chuc_nang   = ;
        // $user   =  Auth::guard('sanctum')->user();
        // $check  =   ChiTietChucNang::where('id_chuc_vu', $user->id_chuc_vu)
        //     ->where('id_chuc_nang', $id_chuc_nang)
        //     ->first();
        // if (!$check) {
        //     return response()->json([
        //         'status'    =>  false,
        //         'message'   =>  'Bạn không đủ quyền truy cập chức năng này!',
        //     ]);
        // }
        try {
            if ($request->tinh_trang == 1) {
                $tinh_trang_moi = 0;
            } else {
                $tinh_trang_moi = 1;
            }
            NhanVien::where('id', $request->id)->update([
                'tinh_trang'    =>  $tinh_trang_moi
            ]);
            return response()->json([
                'status'            =>   true,
                'message'           =>   'Đã đổi trạng thái thành công',
            ]);
        } catch (Exception $e) {
            Log::info("Lỗi", $e);
            return response()->json([
                'status'            =>   false,
                'message'           =>   'Có lỗi khi đổi trạng thái',
            ]);
        }
    }

    public function checkNguoiDung()
    {
        $user = Auth::guard('sanctum')->user();
        $check = 0; // mặc định đầu tiên hắn là admin trước
        if($user &&  $user instanceof NhaSanXuat) {
            $check = 1; // hắn là nsx
        } elseif ($user &&  $user instanceof DaiLy){
            $check = 2; // hắn là đại lý
        }

        return response()->json([
            'data' => $check
        ]);
    }
}
