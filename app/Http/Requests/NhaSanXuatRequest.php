<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NhaSanXuatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "ten_cong_ty"    => "required|max:100",
            "email"          => "required|email|max:50|unique:nha_san_xuats,email",
            "password"       => "required|min:7|max:30",
            "so_dien_thoai"  => "required",
            "tinh_thanh_id"  => "required|integer|exists:tinh_thanhs,id",
            "quan_huyen_id"  => "required|integer|exists:quan_huyens,id",
            "dia_chi"        => "required|string|max:255",
            "tinh_trang"     => "required|boolean",
        ];
    }
    public function messages()
    {
        return [
            "ten_cong_ty.required"   => "Tên công ty là bắt buộc.",
            "ten_cong_ty.max"        => "Tên công ty không vượt quá 100 ký tự.",

            "email.required"         => "Email là bắt buộc.",
            "email.email"            => "Email không đúng định dạng.",
            "email.max"              => "Email không vượt quá 30 ký tự.",
            "email.unique"           => "Email đã tồn tại trong hệ thống.",

            "password.required"      => "Mật khẩu là bắt buộc.",
            "password.min"           => "Mật khẩu phải có ít nhất 7 ký tự.",
            "password.max"           => "Mật khẩu không vượt quá 30 ký tự.",

            "so_dien_thoai.required" => "Số điện thoại là bắt buộc.",

            "tinh_thanh_id.required" => "Tỉnh/Thành là bắt buộc.",
            "tinh_thanh_id.integer"  => "Tỉnh/Thành không hợp lệ.",
            "tinh_thanh_id.exists"   => "Tỉnh/Thành không tồn tại.",

            "quan_huyen_id.required" => "Quận/Huyện là bắt buộc.",
            "quan_huyen_id.integer"  => "Quận/Huyện không hợp lệ.",
            "quan_huyen_id.exists"   => "Quận/Huyện không tồn tại.",

            "dia_chi.required"       => "Địa chỉ là bắt buộc.",
            "dia_chi.string"         => "Địa chỉ không hợp lệ.",
            "dia_chi.max"            => "Địa chỉ không vượt quá 255 ký tự.",

            "tinh_trang.required"    => "Tình trạng là bắt buộc.",
            "tinh_trang.boolean"     => "Tình trạng phải là true hoặc false.",
        ];
    }
}
