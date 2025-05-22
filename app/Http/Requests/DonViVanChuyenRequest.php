<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonViVanChuyenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "ten_cong_ty"       => "required|string|max:50",
            "email"             => "required|string|email|max:100|unique:don_vi_van_chuyens,email",
            "password"          => "required|string|min:7|max:30",
            "so_dien_thoai"     => "required|string|max:20",
            "dia_chi"           => "required|string|max:255",
            "cuoc_van_chuyen"   => "required|numeric|min:0",
            "tinh_trang"        => "required|boolean",
        ];
    }
     public function messages(): array
    {
        return [
            "ten_cong_ty.required"     => "Tên công ty là bắt buộc.",
            "ten_cong_ty.string"       => "Tên công ty không hợp lệ.",
            "ten_cong_ty.max"          => "Tên công ty không vượt quá 50 ký tự.",

            "email.required"           => "Email là bắt buộc.",
            "email.string"             => "Email không hợp lệ.",
            "email.email"              => "Email không đúng định dạng.",
            "email.max"                => "Email không vượt quá 100 ký tự.",
            "email.unique"             => "Email đã tồn tại trong hệ thống.",

            "password.required"        => "Mật khẩu là bắt buộc.",
            "password.string"          => "Mật khẩu phải là chuỗi ký tự.",
            "password.min"             => "Mật khẩu phải có ít nhất 7 ký tự.",
            "password.max"             => "Mật khẩu không vượt quá 30 ký tự.",

            "so_dien_thoai.required"   => "Số điện thoại là bắt buộc.",
            "so_dien_thoai.string"     => "Số điện thoại không hợp lệ.",
            "so_dien_thoai.max"        => "Số điện thoại không vượt quá 20 ký tự.",

            "dia_chi.required"         => "Địa chỉ là bắt buộc.",
            "dia_chi.string"           => "Địa chỉ không hợp lệ.",
            "dia_chi.max"              => "Địa chỉ không vượt quá 255 ký tự.",

            "cuoc_van_chuyen.required" => "Cước vận chuyển là bắt buộc.",
            "cuoc_van_chuyen.numeric"  => "Cước vận chuyển phải là số.",
            "cuoc_van_chuyen.min"      => "Cước vận chuyển không được âm.",

            "tinh_trang.required"      => "Tình trạng là bắt buộc.",
            "tinh_trang.boolean"       => "Tình trạng phải là true hoặc false.",
        ];
    }
}
