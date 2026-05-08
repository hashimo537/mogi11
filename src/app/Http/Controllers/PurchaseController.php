<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\Auth;


class PurchaseController extends Controller
{
    public function show(Item $item)
    {
    $address = session()->has('shipping_address_' . $item->id)
            ? (object) session('shipping_address_' . $item->id)
            : Auth::user()->profile;  // profiles テーブルの住所
        return view('login/purchase', compact('item', 'address'));

    }

    public function store(PurchaseRequest $request, Item $item)
    {
        // 購入レコードを保存
        Purchase::create([
            'user_id'        => Auth::id(),
            'item_id'        => $item->id,
            'payment_method' => $request->payment_method,
            // セッションに配送先があれば shipping_addresses テーブルに保存済みのIDを紐付ける
            // （シンプル実装として今回はプロフィール住所をそのまま使用）
        ]);
 
        // 商品を「売り切れ」にする
        $item->update(['is_sold' => true]);
 
        // セッションの一時住所をクリア
        session()->forget('shipping_address_' . $item->id);
 
        // 仕様: 購入後は商品一覧へリダイレクト
        return redirect()->route('items.item')
            ->with('success', '商品を購入しました。');
    }


public function editAddress(Item $item)
    {
        $address = session()->has('shipping_address_' . $item->id)
            ? (object) session('shipping_address_' . $item->id)
            : Auth::user()->profile;
 
        return view('purchase_address', compact('item', 'address'));
    }

    public function updateAddress(ShippingAddressRequest $request, Item $item)
    {
        // 変更住所をセッションに一時保存
        session([
            'shipping_address_' . $item->id => [
                'postal_code' => $request->postal_code,
                'address'     => $request->address,
                'building'    => $request->building,
            ]
        ]);
 
        // 購入画面に戻る
        return redirect()->route('purchase.show', $item->id)
            ->with('success', '配送先を変更しました。');
    }
}