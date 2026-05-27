<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;

class VerifyEmailResponse implements VerifyEmailResponseContract
{
    public function toResponse($request)
    {
        $user = auth()->user();

        if (!$user->profile) {
            return redirect('/mypage/profile');
        }

        return redirect('/');
    }
}