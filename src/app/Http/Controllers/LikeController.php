<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Comment;
use Illuminate\Http\Request;


class LikeController extends Controller
{
    public function toggle(Item $item)
    {
        $user = auth()->user();

        if ($item->likedUsers()->where('user_id', $user->id)->exists()) {
            // すでにいいね済み → 取り消し
            $item->likedUsers()->detach($user->id);
            $liked = false;
        } else {
            // いいね追加
            $item->likedUsers()->attach($user->id);
            $liked = true;
        }

        return back();
    }

    public function store(Request $request, Item $item)
    {
        $request->validate([
    'comment' => 'required|string|max:255',
    ]);

    Comment::create([
    'user_id' => auth()->id(),
    'item_id' => $item->id,
    'comment' => $request->comment,
    ]);

    return redirect()->route('items.show', $item->id);  
    }
}