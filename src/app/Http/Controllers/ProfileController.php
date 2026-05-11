<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    // 画面表示
    public function edit()
    {
        return view('login.profile');
    }

    // 更新処理
public function update(ProfileRequest $request)
{
    $user = auth()->user();

    // ユーザー名更新
    $user->update([
        'name' => $request->name,
    ]);

    // 画像パスを先に準備
    $profileData = [
        'postal_code' => $request->postal_code,
        'address'     => $request->address,
        'building'    => $request->building,
    ];

    // 画像がアップロードされた場合のみ追加
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('profiles', 'public');
        $profileData['image'] = $path;
    }

    // まとめてupdateOrCreate
    $user->profile()->updateOrCreate(
        ['user_id' => $user->id],
        $profileData
    );

    return redirect('/mypage');
}
}
