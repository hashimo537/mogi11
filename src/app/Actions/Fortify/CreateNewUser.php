<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
    'name'                  => ['required', 'string', 'max:20'],
    'email'                 => ['required', 'email'],
    'password'              => ['required', 'min:8', 'confirmed'],
], [
    'name.required'     => 'ユーザー名を入力してください',
    'name.max'          => 'ユーザー名は20文字以内で入力してください',
    'email.required'    => 'メールアドレスを入力してください',
    'email.email'       => '有効なメールアドレスを入力してください',
    'password.required' => 'パスワードを入力してください',
    'password.min'      => 'パスワードは8文字以上で入力してください',
])->validate();
        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
