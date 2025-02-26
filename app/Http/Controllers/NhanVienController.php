<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class NhanVienController extends Controller
{
    public function login(Request $request)
    {
        $check  = Auth::guard('nhan_vien')->attempt(['email' => $request->email, 'password' => $request->password]);
        // check sẽ trả về true hoặc false
        if ($check == true) {  // có
            // Lấy thông tin người đã đăng nhập
            $user  = Auth::guard('nhan_vien')->user();
            $token = $user->createToken('api-token-nhanvien')->plainTextToken;
            return response()->json([
                'message'   =>  'Đăng nhập thành công!',
                'status'    =>  true,
                'token'     =>  $token,
                'user'      =>  $user,
            ]);
        } else {
            return response()->json([
                'message'   =>  'Đăng nhập thất bại!',
                'status'    =>  false
            ]);
        }
    }

    public function check(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $agent = new Agent();
            $device     = $agent->device();
            $os         = $agent->platform();
            $browser    = $agent->browser();

            $check_user =  DB::table('personal_access_tokens')
                ->where('id', $user->currentAccessToken()->id)
                ->first();

            if ($check_user->tokenable_type === "App\\Models\\NhanVien") {
                DB::table('personal_access_tokens')
                    ->where('id', $user->currentAccessToken()->id)
                    ->update([
                        'ip'            =>  request()->ip(),
                        'device'        =>  $device,
                        'os'            =>  $os,
                        'trinh_duyet'   =>  $browser,
                    ]);
                return response()->json([
                    'email'     =>  $user->email,
                    'ho_ten'    =>  $user->ho_ten,
                    'list'      =>  $user->tokens,
                ], 200);
            }

            return response()->json([
                'message'   =>  'Bạn Chưa Đăng Nhập ADMIN',
                'status'    =>  false,
            ], 401);
        } else {
            return response()->json([
                'message'   =>  'Bạn Cần Đăng Nhập Hệ Thống',
                'status'    =>  false,
            ], 401);
        }
    }

    public function logout()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $check_user =  DB::table('personal_access_tokens')
                            ->where('id', $user->currentAccessToken()->id)
                            ->first();
            if($check_user->tokenable_type === "App\\Models\\NhanVien") {
                DB::table('personal_access_tokens')
                    ->where('id', $user->currentAccessToken()->id)
                    ->delete();
                return response()->json([
                    'message'   =>  'Đăng xuất thành công!',
                    'status'    =>  true,
                ], 200);
            }

            return response()->json([
                'message'   =>  'Bạn cần đăng nhập!',
                'status'    =>  false,
            ], 401);
        } else {
            return response()->json([
                'message'   =>  'Bạn cần đăng nhập!',
                'status'    =>  false,
            ]);
        }
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
        // Lấy thông tin từ Authorization : 'Bearer ' gửi lên
        $user = Auth::guard('sanctum')->user();
        if($user && $user instanceof \App\Models\NhanVien) {
            return response()->json([
                'status'    =>  true,
                'message'   =>  "Oke, bạn có thể đi qua",
            ]);
        } else {
            return response()->json([
                'status'    =>  false,
                'message'   =>  "Bạn cần đăng nhập hệ thống trước",
            ]);
        }
    }
}
