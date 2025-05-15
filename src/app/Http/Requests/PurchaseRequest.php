<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
        $rules = [
            'payment_method' => ['required'],  // 支払い方法は必須
        ];

        // 商品に紐づく配送先が存在しない場合のみ、shipment_address_id を必須にする
        $item = $this->route('item'); // ルートの {item} を取得

        $hasAddress = \App\Models\ShipmentAddress::where('item_id', $item->id)->exists();

        if (!$hasAddress) {
            $rules['shipment_address_id'] = ['required', 'exists:shipment_addresses,id'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください。',
            'shipment_address_id.required' => '配送先住所が登録されていません。先に「変更する」ボタンから住所を登録してください。',
            'shipment_address_id.exists' => '選択した配送先が存在しません。',
        ];
    }

}
