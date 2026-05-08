<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;

class MypageController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 出品した商品
        $sellItems = $user->items;

        // 購入した商品
        $buyItems = Purchase::with('item')
            ->where('user_id', $user->id)
            ->get();

        return view('login/mypage', compact('user', 'sellItems', 'buyItems'));
    }
}
