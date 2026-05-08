<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index()
{
    $search = request('search');

    if (request('tab') === 'mylist' && auth()->check()) {
        $items = auth()->user()->likedItems()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->get();
    } else {
        $items = Item::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->get();
    }

    return view('items.item', compact('items'));

}

    public function show(Item $item)
    {
        $item->load(['likedUsers', 'comments.user', 'categories']);

        return view('items.show', compact('item'));
    }

    public function create()
    {
        $categories = Category::all();
        $conditions = Item::conditionLabels();
        return view('items.sell' , compact('categories' , 'conditions'));
    }

    public function store(Request $request)
{
    // バリデーション
    $validated = $request->validate([
        'name' => 'required|max:255',
        'price' => 'required|integer',
        'description' => 'required',
        'image' => 'required|image',
        'categories' => 'required|array',
    ]);

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
