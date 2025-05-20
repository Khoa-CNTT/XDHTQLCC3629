<?php

namespace App\Http\Controllers;

use App\Models\QuanHuyen;
use Illuminate\Http\Request;

class QuanHuyenController extends Controller
{
    public function getData()
    {
        $data = QuanHuyen::get();

        return response()->json([
            'status'    =>  true,
            'quan_huyen' => $data
        ]);
    }
}
