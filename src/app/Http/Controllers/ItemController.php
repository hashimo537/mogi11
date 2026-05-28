<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;

class ItemController extends Controller
{
    // 商品一覧表示
public function index(Request $request)
{
    $query = Item::query();

    // 自分が出品した商品を除外
    if (auth()->check()) {
        $query->where('user_id', '!=', auth()->id());
    }

    // 検索
    if ($request->search) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // マイリスト（いいねした商品）
    if ($request->tab === 'mylist') {
    if (auth()->check()) {
    $likedItemIds = auth()->user()->likes()->pluck('item_id');
    $query->whereIn('id', $likedItemIds);
        } else {
    // 未ログインは何も表示しない
    $query->whereRaw('1 = 0');
    }
        }
    $items = $query->get();

    return view('items.item', compact('items'));
}


    // 商品詳細表示
    public function show(Item $item)
    {
        $item->load(['likedUsers', 'comments.user', 'categories']);

        return view('items.show', compact('item'));
    }

    // 出品画面表示

    public function create()
    {
        $categories = Category::all();
        $conditions = Item::conditionLabels();
        return view('items.sell' , compact('categories' , 'conditions'));
    }


    public function store(ExhibitionRequest $request)
{

    // 画像保存
    $path = $request->file('image')->store('items', 'public');

    // Item作成
    $item = Item::create([
        'user_id' => Auth::id(),
        'name' => $request->name,
        'brand' => $request->brand,
        'description' => $request->description,
        'price' => $request->price,
        'condition' => $request->condition,
        'image' => $path,
    ]);

    // カテゴリ紐付け（多対多）
    $item->categories()->attach($request->categories);

    // リダイレクト
    return redirect('/')->with('success', '出品しました！');
}

}
