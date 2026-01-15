<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('items')->insert([
            [
                'user_id' => 1,
                'name' => '腕時計',
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'price' => 15000,
                'condition' => 1,
                'image_path' => 'items/Armani+Mens+Clock.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'HDD',
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'price' => 5000,
                'condition' => 2,
                'image_path' => 'items/HDD+Hard+Disk.jpg','created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'user_id' => 1,
                'name' => '玉ねぎ３束',
                'brand' => 'なし',
                'description' => '新鮮な玉ねぎの３束セット',
                'price' => 300,
                'condition' => 3,
                'image_path' => 'items/iLoveIMG+d.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => '革靴',
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'price' => 15000,
                'condition' => 4,
                'image_path' => 'items/Leather+Shoes+Product+Photo.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'ノートPC',
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'price' => 45000,
                'condition' => 1,
                'image_path' => 'items/Living+Room+Laptop.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'マイク',
                'brand' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'price' => 8000,
                'condition' => 2,
                'image_path' => 'items/Music+Mic+4632231.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'ショルダーバッグ',
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'price' => 3500,
                'condition' => 3,
                'image_path' => 'items/Purse+fashion+pocket.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'タンブラー',
                'brand' => 'なし',
                'description' => '使いやすいタンブラー',
                'price' => 500,
                'condition' => 4,
                'image_path' => 'items/Tumbler+souvenir.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'コーヒーミル',
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'price' => 4000,
                'condition' => 1,
                'image_path' => 'items/Waitress+with+Coffee+Grinder.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'name' => 'メイクセット',
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'price' => 2500,
                'condition' => 2,
                'image_path' => 'items/外出メイクアップセット.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
