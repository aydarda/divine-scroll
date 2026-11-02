<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Throwable;

class PasswordController extends Controller
{
    // POST /api/v1/auth/forgot-password  { "email": "..." }
    // The email's link opens the website's reset page. The answer is the same whether or not the account exists.
    public function forgot(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        try {
            Password::sendResetLink(['email' => strtolower($request->input('email'))]);
        } catch (Throwable $e) {
            Log::error('Password reset mail failed: ' . $e->getMessage());
        }

        return response()->json(['message' => 'If that address has an account, a reset link is on its way.']);
    }

    // POST /api/v1/auth/reset-password  { token, email, password, password_confirmation }
    public function reset(Request $request)
    {
        $data = $request->validate([
            'token'    => ['required', 'string'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(10)],
        ]);

        $status = Password::reset(
            [
                'email'                 => strtolower($data['email']),
                'password'              => $data['password'],
                'password_confirmation' => $request->input('password_confirmation'),
                'token'                 => $data['token'],
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete(); // sign the person out everywhere
                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => ['This reset link is invalid or has expired.']]);
        }

        return response()->json(['message' => 'Your password was changed. You can sign in now.']);
    }
}
