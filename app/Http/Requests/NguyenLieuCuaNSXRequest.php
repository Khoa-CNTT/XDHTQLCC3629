<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NguyenLieuCuaNSXRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "ma_nguyen_lieu"    => "required|string|max:50",
            "ten_nguyen_lieu"   => "required|string|max:100",
            "so_luong"          => "required|numeric|min:0",
            "don_vi_tinh"       => "required|string|max:20",
            "ngay_san_xuat"     => "required|date",
            "han_su_dung"       => "required|date|after_or_equal:ngay_san_xuat",
        ];
    }
    public function messages()
    {
        return [
            "ma_nguyen_lieu.required"    => "Mã nguyên liệu là bắt buộc.",
            "ma_nguyen_lieu.string"      => "Mã nguyên liệu không hợp lệ.",
            "ma_nguyen_lieu.max"         => "Mã nguyên liệu không vượt quá 50 ký tự.",

            "ten_nguyen_lieu.required"   => "Tên nguyên liệu là bắt buộc.",
            "ten_nguyen_lieu.string"     => "Tên nguyên liệu không hợp lệ.",
            "ten_nguyen_lieu.max"        => "Tên nguyên liệu không vượt quá 100 ký tự.",

            "so_luong.required"          => "Số lượng là bắt buộc.",
            "so_luong.numeric"           => "Số lượng phải là số.",
            "so_luong.min"               => "Số lượng không được âm.",

            "don_vi_tinh.required"       => "Đơn vị tính là bắt buộc.",
            "don_vi_tinh.string"         => "Đơn vị tính không hợp lệ.",
            "don_vi_tinh.max"            => "Đơn vị tính không vượt quá 20 ký tự.",

            "ngay_san_xuat.required"     => "Ngày sản xuất là bắt buộc.",
            "ngay_san_xuat.date"         => "Ngày sản xuất không đúng định dạng ngày.",

            "han_su_dung.required"       => "Hạn sử dụng là bắt buộc.",
            "han_su_dung.date"           => "Hạn sử dụng không đúng định dạng ngày.",
            "han_su_dung.after_or_equal" => "Hạn sử dụng phải sau hoặc bằng ngày sản xuất.",
        ];
    }
}
