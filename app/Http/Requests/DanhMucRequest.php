<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DanhMucRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "ma_danh_muc"    => "required|string|max:50|unique:danh_muc_san_phams,ma_danh_muc",
            "ten_danh_muc"   => "required|string|max:100|unique:danh_muc_san_phams,ten_danh_muc",
            "tinh_trang"     => "required|boolean",
        ];
    }
    public function messages(): array
    {
        return [
            "ma_danh_muc.required"    => "Mã danh mục là bắt buộc.",
            "ma_danh_muc.string"      => "Mã danh mục không hợp lệ.",
            "ma_danh_muc.max"         => "Mã danh mục không vượt quá 50 ký tự.",
            "ma_danh_muc.unique"      => "Mã danh mục đã tồn tại trong hệ thống.",


            "ten_danh_muc.required"   => "Tên danh mục là bắt buộc.",
            "ten_danh_muc.string"     => "Tên danh mục không hợp lệ.",
            "ten_danh_muc.max"        => "Tên danh mục không vượt quá 100 ký tự.",
            "ten_danh_muc.unique"     => "Tên danh mục đã tồn tại trong hệ thống.",

            "tinh_trang.required"     => "Tình trạng là bắt buộc.",
            "tinh_trang.boolean"      => "Tình trạng phải là true hoặc false.",
        ];
    }
}
