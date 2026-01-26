<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): boot
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'image_path' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048',
            ],

            'postcode' => [
                'required',
                'regex:/^\d{3}-\d{4}$/',
            ],

            'address' => ['required', 'string'],
            'building' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ユーザー名を入力してください',

            'image_path.image' => '画像ファイルを選択してください',
            'image_path.mimes' => 'プロフィール画像は jpeg または png のみです',

            'postcode.required' => '郵便番号を入力してください',
            'postcode.regex' => '郵便番号は「123-4567」の形式で入力してください',

            'address.required' => '住所を入力してください',
        ];
    }
}
