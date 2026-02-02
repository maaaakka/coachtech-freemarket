<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'        => ['required'],
            'description' => ['required', 'max:255'],
            'image_path'  => ['required', 'image', 'mimes:jpeg,png'],
            'categories'  => ['required', 'array'],
            'categories.*'=> ['exists:categories,id'],
            'condition'   => ['required'],
            'price'       => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => '商品名は必須です',
            'description.required' => '商品説明は必須です',
            'description.max'      => '商品説明は255文字以内で入力してください',
            'image_path.required'  => '商品画像は必須です',
            'image_path.image'     => '画像ファイルを選択してください',
            'image_path.mimes'     => '画像はjpegまたはpng形式のみ対応しています',
            'categories.required'  => 'カテゴリーを選択してください',
            'condition.required'   => '商品の状態を選択してください',
            'price.required'       => '価格は必須です',
            'price.integer'        => '価格は数値で入力してください',
            'price.min'            => '価格は0円以上で入力してください',
        ];
    }
}