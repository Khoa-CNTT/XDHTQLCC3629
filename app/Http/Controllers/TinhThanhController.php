<?php

namespace App\Http\Controllers;

use App\Models\TinhThanh;
use Illuminate\Http\Request;

class TinhThanhController extends Controller
{
    public function getData()
    {
        $data = TinhThanh::get();

        return response()->json([
            'status'    =>  true,
            'tinh_thanh' => $data
        ]);
    }
}
