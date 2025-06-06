<?php

namespace App\Http\Controllers;

use App\Http\Requests\NhanVienRequest;
use App\Models\DaiLy;
use App\Models\DonViVanChuyen;
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
        $list       = ['nhan_vien', 'nha_san_xuat', 'dai_ly', 'don_vi_van_chuyen'];
        $list_token = ['api_token_nhanvien', 'api_token_nhasanxuat', 'api_token_daily', 'api_token_donvivanchuyen'];
        $list_model = [
            NhanVien::class,
            NhaSanXuat::class,
            DaiLy::class,
            DonViVanChuyen::class
        ];

        for ($i = 0; $i < count($list); $i++) {
            $check = Auth::guard($list[$i])->attempt([
                'email'     => $request->email,
                'password'  => $request->password,
            ]);

            if ($check) {
                $user = Auth::guard($list[$i])->user();

                // ✅ Kiểm tra nếu tài khoản bị tạm dừng
                if ($user && isset($user->tinh_trang) && $user->tinh_trang == 0) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Tài khoản của bạn đã bị tạm dừng!'
                    ], 403); // trả về mã lỗi 403 (forbidden)
                }

                if ($user && $user instanceof $list_model[$i]) {
                    return response()->json([
                        'status'  => true,
                        'message' => 'Đăng nhập thành công!',
                        'token'   => $user->createToken($list_token[$i])->plainTextToken
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
            "App\\Models\\NhaSanXuat",
            "App\\Models\\DonViVanChuyen",
        ];

        if (!in_array($check_user->tokenable_type, $valid_types)) {
            return response()->json([
                'message'   => 'Bạn Chưa Đăng Nhập',
                'status'    => false,
            ], 401);
        }

        //lấy được số dư của nsx, đvvc
        $so_du_tai_khoan = null;

        if ($check_user->tokenable_type === "App\\Models\\NhaSanXuat" || $check_user->tokenable_type === "App\\Models\\DonViVanChuyen") {
            $so_du_tai_khoan = $user->so_du_tai_khoan;
        }

        DB::table('personal_access_tokens')
            ->where('id', $user->currentAccessToken()->id)
            ->update([
                'ip'         => request()->ip(),
                'device'     => $device,
                'os'         => $os,
                'trinh_duyet' => $browser,
            ]);

        return response()->json([
            'email'             =>      $user->email,
            'ho_ten'            =>      $user->ho_ten ?? $user->ten_cong_ty,
            'list'              =>      $user->tokens,
            'loai_tai_khoan'    =>      $user->loai_tai_khoan,
            'so_dien_thoai'     =>      $user->so_dien_thoai,
            'dia_chi'           =>      $user->dia_chi,
            'user_id'           =>      $user->id,
            'dia_chi_vi'        =>      $user->dia_chi_vi,
            'so_du_tai_khoan'   =>      $so_du_tai_khoan
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
            "App\\Models\\NhaSanXuat",
            "App\\Models\\DonViVanChuyen",
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

        if (
            $user instanceof \App\Models\NhanVien ||
            $user instanceof \App\Models\DaiLy ||
            $user instanceof \App\Models\NhaSanXuat ||  $user instanceof \App\Models\DonViVanChuyen
        ) {

            $so_du = null;

            if (
                $user instanceof \App\Models\NhaSanXuat ||
                $user instanceof \App\Models\DonViVanChuyen || $user instanceof \App\Models\NhanVien
            ) {
                $so_du = $user->so_du_tai_khoan;
            }

            return response()->json([
                'status'  => true,
                'message' => "Oke, bạn có thể đi qua",
                'so_du_tai_khoan' => $so_du,
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
        $data = NhanVien::get();

        return response()->json([
            'status'    =>  true,
            'nhan_vien' => $data
        ]);
    }

    public function createNhanVien(NhanVienRequest $request)
    {
        NhanVien::create([
            'ho_ten'    =>  $request->ho_ten,
            'email'  =>  $request->email,
            'password'       =>  bcrypt($request->password),
            'id_chuc_vu' =>  $request->id_chuc_vu,
            'tinh_trang'    =>  $request->tinh_trang,
            'loai_tai_khoan'   => 'Nhân Viên',
            'dia_chi_vi'    =>  "TGdU79UeooERfKuVYm9RPLJXRbG9zPBHSd"
        ]);
        return response()->json([
            'status'    =>  true,
            'message'   =>  'Đã tạo mới nhân viên thành công!'
        ]);
    }

    public function updateNhanVien(Request $request)
    {
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
        if ($user &&  $user instanceof NhaSanXuat) {
            $check = 1; // hắn là nsx
        } elseif ($user &&  $user instanceof DaiLy) {
            $check = 2; // hắn là đại lý
        } elseif ($user &&  $user instanceof DonViVanChuyen) {
            $check = 3; // hắn là đại lý
        }

        return response()->json([
            'data' => $check
        ]);
    }
}
