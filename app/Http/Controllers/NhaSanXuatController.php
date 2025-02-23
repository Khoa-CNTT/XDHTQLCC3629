<?php

namespace App\Http\Controllers;

use App\Models\NhaSanXuat;
use Illuminate\Http\Request;

class NhaSanXuatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view();
    }

    public function getData()
    {
        // $id_chuc_nang   = 1;
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
        $data = NhaSanXuat::get();

        return response()->json([
            'status'    =>  true,
            'nha_san_xuat' => $data
        ]);
    }
}
