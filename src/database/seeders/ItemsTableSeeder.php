<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::where('name', '佐藤 花子')->first();
        $user2 = User::where('name', '鈴木 太郎')->first();

        // 商品1：腕時計（ファッション・メンズ）
        $item1 = Item::create([
            'user_id' => $user2->id,
            'name' => '腕時計',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'price' => 15000,
            'status' => '良好',
            'brand_name' => 'ARMANI',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
            'sold' => false,
        ]);
        $item1->categories()->attach([
            Category::where('name', 'ファッション')->first()->id,
            Category::where('name', 'メンズ')->first()->id,
        ]);

        // 商品2：HDD（家電）
        $item2 = Item::create([
            'user_id' => $user1->id,
            'name' => 'HDD',
            'description' => '高速で信頼性の高いハードディスク',
            'price' => 5000,
            'status' => '目立った傷や汚れなし',
            'brand_name' => 'Western Digital',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
            'sold' => false,
        ]);
        $item2->categories()->attach([
            Category::where('name', '家電')->first()->id,
        ]);

        // 商品3：玉ねぎ3束（食品）
        $item3 = Item::create([
            'user_id' => $user1->id,
            'name' => '玉ねぎ3束',
            'description' => '新鮮な玉ねぎ3束のセット',
            'price' => 300,
            'status' => 'やや傷や汚れあり',
            'brand_name' => null,
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
            'sold' => false,
        ]);
        $item3->categories()->attach([
            Category::where('name', '食品')->first()->id,
        ]);

        // 商品4：革靴（ファッション・メンズ）
        $item4 = Item::create([
            'user_id' => $user2->id,
            'name' => '革靴',
            'description' => 'クラシックなデザインの革靴',
            'price' => 4000,
            'status' => '状態が悪い',
            'brand_name' => 'REGAL',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
            'sold' => false,
        ]);
        $item4->categories()->attach([
            Category::where('name', 'ファッション')->first()->id,
            Category::where('name', 'メンズ')->first()->id,
        ]);

        // 商品5：ノートPC（家電）
        $item5 = Item::create([
            'user_id' => $user2->id,
            'name' => 'ノートPC',
            'description' => '高性能なノートパソコン',
            'price' => 45000,
            'status' => '良好',
            'brand_name' => 'DELL',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
            'sold' => false,
        ]);
        $item5->categories()->attach([
            Category::where('name', '家電')->first()->id,
        ]);

        // 商品6：マイク（家電）
        $item6 = Item::create([
            'user_id' => $user2->id,
            'name' => 'マイク',
            'description' => '高音質のレコーディング用マイク',
            'price' => 8000,
            'status' => '目立った傷や汚れなし',
            'brand_name' => 'Blue',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
            'sold' => false,
        ]);
        $item6->categories()->attach([
            Category::where('name', '家電')->first()->id,
        ]);

        // 商品7：ショルダーバッグ（ファッション・レディース）
        $item7 = Item::create([
            'user_id' => $user1->id,
            'name' => 'ショルダーバッグ',
            'description' => 'おしゃれなショルダーバッグ',
            'price' => 3500,
            'status' => 'やや傷や汚れあり',
            'brand_name' => 'Nine West',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
            'sold' => false,
        ]);
        $item7->categories()->attach([
            Category::where('name', 'ファッション')->first()->id,
            Category::where('name', 'レディース')->first()->id,
        ]);

        // 商品8：タンブラー（キッチン）
        $item8 = Item::create([
            'user_id' => $user1->id,
            'name' => 'タンブラー',
            'description' => '使いやすいタンブラー',
            'price' => 500,
            'status' => '状態が悪い',
            'brand_name' => 'THERMOS',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
            'sold' => false,
        ]);
        $item8->categories()->attach([
            Category::where('name', 'キッチン')->first()->id,
        ]);

        // 商品9：コーヒーミル（キッチン）
        $item9 = Item::create([
            'user_id' => $user2->id,
            'name' => 'コーヒーミル',
            'description' => '手動のコーヒーミル',
            'price' => 4000,
            'status' => '良好',
            'brand_name' => 'Kalita',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
            'sold' => false,
        ]);
        $item9->categories()->attach([
            Category::where('name', 'キッチン')->first()->id,
        ]);

        // 商品10：メイクセット（コスメ）
        $item10 = Item::create([
            'user_id' => $user1->id,
            'name' => 'メイクセット',
            'description' => '便利なメイクアップセット',
            'price' => 2500,
            'status' => '目立った傷や汚れなし',
            'brand_name' => 'CANMAKE',
            'image_path' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
            'sold' => false,
        ]);
        $item10->categories()->attach([
            Category::where('name', 'コスメ')->first()->id,
        ]);
    }
}
