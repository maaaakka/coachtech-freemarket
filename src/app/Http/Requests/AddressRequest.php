<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /**
     * 認可（今回はログイン中ユーザーなので true）
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール
     */
    public function rules(): array
    {
        return [
            'postcode' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address'  => ['required', 'string'],
            'building' => ['nullable', 'string'],
        ];
    }

    /**
     * エラーメッセージ
     */
    public function messages(): array
    {
        return [
            'postcode.required' => '郵便番号を入力してください',
            'postcode.regex'   => '郵便番号は「123-4567」の形式で入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}