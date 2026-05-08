<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Item;


class ItemSeeder extends Seeder
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
                'condition' => Item::CONDITION_GOOD,
                'image' => 'menclock.jpg',
            ],
            [
                'user_id' => 2,
                'name' => 'HDD',
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'price' => 5000,
                'condition' => Item::CONDITION_CLEAN,
                'image' => 'harddisk.jpg',
            ],
            [
                'user_id' => 1,
                'name' => '玉ねぎ3束',
                'brand' => null,
                'description' => '新鮮な玉ねぎ3束のセット',
                'price' => 300,
                'condition' => Item::CONDITION_NORMAL,
                'image' => 'onion.jpg',
            ],
            [
                'user_id' => 1,
                'name' => '革靴',
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'price' => 4000,
                'condition' => Item::CONDITION_BAD,
                'image' => 'leathershoes.jpg',
            ],
            [
                'user_id' => 2,
                'name' => 'ノートPC',
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'price' => 45000,
                'condition' => Item::CONDITION_GOOD,
                'image' => 'laptop.jpg',
            ],
            [
                'user_id' => 1,
                'name' => 'マイク',
                'brand' => null,
                'description' => '高音質のレコーディング用マイク',
                'price' => 8000,
                'condition' => Item::CONDITION_CLEAN,
                'image' => 'microphone.jpg',
            ],
            [
                'user_id' => 1,
                'name' => 'ショルダーバッグ',
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'price' => 3500,
                'condition' => Item::CONDITION_NORMAL,
                'image' => 'shoulderbag.jpg',
            ],
            [
                'user_id' => 1,
                'name' => 'タンブラー',
                'brand' => null,
                'description' => '使いやすいタンブラー',
                'price' => 500,
                'condition' => Item::CONDITION_BAD,
                'image' => 'tumbler.jpg',
            ],
            [
                'user_id' => 1,
                'name' => 'コーヒーミル',
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'price' => 4000,
                'condition' => Item::CONDITION_GOOD,
                'image' => 'coffeegrinder.jpg',
            ],
            [
                'user_id' => 1,
                'name' => 'メイクセット',
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'price' => 2500,
                'condition' => Item::CONDITION_CLEAN,
                'image' => 'makeupset.jpg',
            ],
        ]);
    }
}
