<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShipmentAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('purchases')->insert([
            'id' => 1,
            'user_id' => 1,
            'item_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('shipment_addresses')->truncate(); // ← 既存を削除してIDをリセット

        DB::table('shipment_addresses')->insert([
            'id' => 1,
            'user_id' => 1, // hanako@example.com の user_id に合わせて変更（必要に応じて）
            'item_id' => 1,
            'purchase_id' => 1,
            'postal_code' => '123-4567',
            'address' => '東京都新宿区1-1-1',
            'building' => 'ハナコマンション101',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
