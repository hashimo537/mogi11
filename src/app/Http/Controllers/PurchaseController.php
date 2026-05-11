<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest; 

class PurchaseController extends Controller
{
    // ========================================
    // 配送先を取得する共通処理
    // ========================================
    private function resolveAddress(Item $item): array
    {
        if (session()->has('shipping_address_' . $item->id)) {
            return session('shipping_address_' . $item->id);
        }

        $profile = Auth::user()->profile;

        return [
            'postal_code' => $profile->postal_code ?? null,
            'address'     => $profile->address ?? null,
            'building'    => $profile->building ?? null,
        ];
    }

    // ========================================
    // 購入画面の表示
    // ========================================
    public function show(Item $item)
    {
        if ($item->is_sold) {
            return redirect()->route('items.index')
                ->with('error', 'この商品はすでに売り切れです。');
        }

        $address = (object) $this->resolveAddress($item);

        return view('login/purchase', compact('item', 'address'));
    }

    // ========================================
    // 購入処理 → Stripe決済画面へリダイレクト
    // ========================================
    public function store(PurchaseRequest $request, Item $item)
    {
        // 二重購入チェック
        if ($item->is_sold) {
            return redirect()->back()->with('error', 'この商品はすでに売り切れです。');
        }

        // 配送先チェック
        $addressData = $this->resolveAddress($item);
        if (empty($addressData['address'])) {
            return redirect()->back()->with('error', '配送先を登録してください。');
        }

        // 支払い方法をセッションに保存（決済成功後に使う）
        session([
            'purchase_payment_method_' . $item->id => $request->payment_method,
        ]);

        // StripeのAPIキーをセット（.envから読み込む）
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        // Stripeの決済画面を作成
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'jpy',       // 日本円
                    'product_data' => [
                        'name' => $item->name,     // 商品名
                    ],
                    'unit_amount'  => $item->price, // 金額
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => route('purchase.success', $item->id), // 決済成功後に戻るURL
            'cancel_url'  => route('purchase.cancel', $item->id),  // キャンセル時に戻るURL
        ]);

        // Stripeの決済画面へリダイレクト
        return redirect($session->url);
    }

    // ========================================
    // 決済成功後の処理
    // ========================================
    public function success(Item $item)
    {
        // 二重購入チェック
        if ($item->is_sold) {
            return redirect()->route('items.index')
                ->with('error', 'この商品はすでに売り切れです。');
        }

        $addressData = $this->resolveAddress($item);
        $paymentMethod = session('purchase_payment_method_' . $item->id);

        // 決済成功後にDBへ保存
        DB::transaction(function () use ($item, $addressData, $paymentMethod) {
            $purchase = Purchase::create([
                'user_id'        => Auth::id(),
                'item_id'        => $item->id,
                'payment_method' => $paymentMethod,
            ]);

            ShippingAddress::create([
                'purchase_id' => $purchase->id,
                'postal_code' => $addressData['postal_code'],
                'address'     => $addressData['address'],
                'building'    => $addressData['building'] ?? null,
            ]);

            $item->update(['is_sold' => true]);
        });

        // セッションを削除
        session()->forget('shipping_address_' . $item->id);
        session()->forget('purchase_payment_method_' . $item->id);

        return redirect()->route('items.index')
            ->with('success', '商品を購入しました。');
    }

    // ========================================
    // 決済キャンセル後の処理
    // ========================================
    public function cancel(Item $item)
    {
        return redirect()->route('purchase.show', $item->id)
            ->with('error', '決済がキャンセルされました。');
    }

    // ========================================
    // 配送先変更画面の表示
    // ========================================
    public function editAddress(Item $item)
    {
        $address = (object) $this->resolveAddress($item);

        return view('login/address', compact('item', 'address'));
    }

    // ========================================
    // 配送先の更新（セッションに保存）
    // ========================================
    public function updateAddress(ShippingAddressRequest $request, Item $item)
    {
        session([
            'shipping_address_' . $item->id => [
                'postal_code' => $request->postal_code,
                'address'     => $request->address,
                'building'    => $request->building,
            ]
        ]);

        return redirect()->route('purchase.show', $item->id)
            ->with('success', '配送先を変更しました。');
    }
}