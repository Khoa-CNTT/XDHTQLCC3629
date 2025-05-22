<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NhanVienRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "ho_ten" => "required|max:100",
            "email" => "required|email|max:30",
            "password" => "required|min:7|max:30",
            "tinh_trang" => "required|boolean",
        ];
    }
    public function messages()
    {
        return [
            'ho_ten.required' => 'Họ tên không được bỏ trống.',
            'ho_ten.max' => 'Họ tên không được vượt quá 100 ký tự.',

            'email.required' => 'Email không được bỏ trống.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 30 ký tự.',

            'password.required' => 'Mật khẩu không được bỏ trống.',
            'password.min' => 'Mật khẩu phải có ít nhất 7 ký tự.',
            'password.max' => 'Mật khẩu không được vượt quá 30 ký tự.',

            'tinh_trang.required' => 'Trạng thái tài khoản là bắt buộc.',
            'tinh_trang.boolean' => 'Trạng thái tài khoản phải là true hoặc false.',
        ];
    }
}
