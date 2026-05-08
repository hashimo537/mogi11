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

        // プロフィール更新 or 作成
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building,
            ]
        );

        return redirect('/mypage');
    }
}
