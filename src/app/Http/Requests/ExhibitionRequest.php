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
    public function authorize()
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
            'name' => ['required', 'string'],
            'description' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpeg,png', 'max:2048'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'condition' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '商品名は必須です。',
            'description.required' => '商品の説明は必須です。',
            'description.max' => '商品の説明は255文字以内で入力してください。',
            'image.required' => '商品画像は必須です。',
            'image.image' => '画像ファイルを選択してください。',
            'image.mimes' => '画像はjpegまたはpng形式である必要があります。',
            'categories.required' => 'カテゴリを1つ以上選択してください。',
            'condition.required' => '商品の状態を選択してください。',
            'price.required' => '販売価格は必須です。',
            'price.numeric' => '販売価格は数値で入力してください。',
            'price.min' => '販売価格は0円以上にしてください。',
        ];
    }
}
