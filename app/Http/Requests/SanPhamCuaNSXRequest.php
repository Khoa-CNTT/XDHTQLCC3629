<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SanPhamCuaNSXRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "ten_san_pham"      => "required|string|max:100",
            "mo_ta"             => "nullable|string|max:500",
            "id_danh_muc"       => "required|exists:danh_mucs,id",
            "so_luong_ton_kho"  => "required|integer|min:0",
            "gia_ban"           => "required|numeric|min:0",
            "don_vi_tinh"       => "required|string|max:50",
        ];
    }
    public function messages()
    {
        return [
            "ten_san_pham.required"       => "Tên sản phẩm không được để trống.",
            "ten_san_pham.max"            => "Tên sản phẩm không được vượt quá 100 ký tự.",

            "mo_ta.max"                   => "Mô tả không được vượt quá 500 ký tự.",

            "id_danh_muc.required"        => "Danh mục sản phẩm là bắt buộc.",
            "id_danh_muc.exists"          => "Danh mục sản phẩm không hợp lệ.",

            "so_luong_ton_kho.required"   => "Số lượng tồn kho là bắt buộc.",
            "so_luong_ton_kho.integer"    => "Số lượng tồn kho phải là số nguyên.",
            "so_luong_ton_kho.min"        => "Số lượng tồn kho không được âm.",

            "gia_ban.required"            => "Giá bán là bắt buộc.",
            "gia_ban.numeric"             => "Giá bán phải là số.",
            "gia_ban.min"                 => "Giá bán không được âm.",

            "don_vi_tinh.required"        => "Đơn vị tính là bắt buộc.",
            "don_vi_tinh.max"             => "Đơn vị tính không được vượt quá 50 ký tự.",
        ];
    }
}
