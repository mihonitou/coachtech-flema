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
            'postal_code' => ['required', 'regex:/^\d{3}-?\d{4}$/'], // 例: 123-4567 or 1234567
            'address' => ['required', 'string'],
            'building' => ['nullable', 'string'],
            'profile_image' => ['nullable', 'file', 'mimes:jpeg,png', 'max:2048'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'ユーザー名を入力してください。',
            'postal_code.required' => '郵便番号を入力してください。',
            'postal_code.regex' => '郵便番号の形式が正しくありません（例：123-4567）。',
            'address.required' => '住所を入力してください。',
            'profile_image.mimes' => 'プロフィール画像はjpegまたはpng形式でアップロードしてください。',
            'profile_image.max' => 'プロフィール画像のサイズは2MB以下にしてください。',
        ];
    }
}
