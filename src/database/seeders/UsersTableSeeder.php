<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon; // これが必要（今の時刻を生成）

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => '佐藤 花子',
            'email' => 'hanako@example.com',
            'password' => Hash::make('password123'),
            'profile_image' => null,
            'postal_code' => '123-4567',
            'address' => '東京都新宿区1-1-1',
            'building' => 'ハナコマンション101',
            'profile_completed' => true, // ✅ プロフィール設定済みにする
            'email_verified_at' => Carbon::now(), // ✅ メール認証済みにする
        ]);

        User::create([
            'name' => '鈴木 太郎',
            'email' => 'taro@example.com',
            'password' => Hash::make('password456'),
            'profile_image' => null,
            'postal_code' => '987-6543',
            'address' => '大阪府大阪市2-2-2',
            'building' => 'スズキビル202',
            'profile_completed' => true,
            'email_verified_at' => Carbon::now(), // ✅ メール認証済みにする
        ]);
    }
}
