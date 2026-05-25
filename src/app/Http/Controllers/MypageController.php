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
        $buyItems = $user->purchases;




        return view('login/mypage', compact('user', 'sellItems', 'buyItems'));
    }
}
