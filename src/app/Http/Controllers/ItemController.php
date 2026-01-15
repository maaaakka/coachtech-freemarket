<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(){
        $items = [
            [
                'id' => 1,
                'image_path' => 'items/Armani+Mens+Clock.jpg',
                'name' => '腕時計',
                'brand' => 'Rolax',
                'price' => 15000,
                'condition' => 1,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
            ],
            [
                'id' => 2,
                'image_path' => 'items/HDD+Hard+Disk.jpg',
                'name' => 'HDD',
                'brand' => '西芝',
                'price' => 5000,
                'condition' => 2,
                'description' => '高速で信頼性の高いハードディスク',
            ],
            [
                'id' => 3,
                'image_path' => 'items/iLoveIMG+d.jpg',
                'name' => '玉ねぎ３束',
                'brand' => 'なし',
                'price' => 300,
                'condition' => 3,
                'description' => '新鮮な玉ねぎの３束セット',
            ],
            [
                'id' => 4,
                'image_path' => 'items/Leather+Shoes+Product+Photo.jpg',
                'name' => '革靴',
                'brand' => null,
                'price' => 15000,
                'condition' => 4,
                'description' => 'クラシックなデザインの革靴',
            ],
            [
                'id' => 5,
                'image_path' => 'items/Living+Room+Laptop.jpg',
                'name' => 'ノートPC',
                'brand' => null,
                'price' => 45000,
                'condition' => 1,
                'description' => '高性能なノートパソコン',
            ],
            [
                'id' => 6,
                'image_path' => 'items/Music+Mic+4632231.jpg',
                'name' => 'マイク',
                'brand' => 'なし',
                'price' => 8000,
                'condition' => 2,
                'description' => '高音質のレコーディング用マイク',
            ],
            [
                'id' => 7,
                'image_path' => 'items/Purse+fashion+pocket.jpg',
                'name' => 'ショルダーバッグ',
                'brand' => null,
                'price' => 3500,
                'condition' => 3,
                'description' => 'おしゃれなショルダーバッグ',
            ],
            [
                'id' =>8,
                'image_path' => 'items/Tumbler+souvenir.jpg',
                'name' => 'タンブラー',
                'brand' => 'なし',
                'price' => 500,
                'condition' => 4,
                'description' => '使いやすいタンブラー',
            ],
            [
                'id' => 9,
                'image_path' => 'items/Waitress+with+Coffee+Grinder.jpg',
                'name' => 'コーヒーミル',
                'brand' => 'Starbacks',
                'price' => 4000,
                'condition' => 1,
                'description' => '手動のコーヒーミル',
            ],
            [
                'id' => 10,
                'image_path' => 'items/外出メイクアップセット.jpg',
                'name' => 'メイクセット',
                'brand' => null,
                'price' => 2500,
                'condition' => 2,
                'description' => '便利なメイクアップセット',
            ],
    ];
        return view('items.index', compact('items'));
    }

    public function show($id)
    {
        $item = [
            'id' => $id,
            'image_path' => 'items/Armani+Mens+Clock.jpg',
            'name' => '商品名がここに入る',
            'brand' => 'ブランド名',
            'price' => 47000,
            'description' => '商品説明がここに入ります。',
            'condition' => 1,
            'categories' => ['洋服', 'メンズ'],
            'likes_count' => 3,
            'comments_count' => 1,
            'comments' => [
                [
                    'user' => 'admin',
                    'body' => 'こちらにコメントが入ります。',
                ],
            ],
        ];

        return view('items.show', compact('item'));
    }
}