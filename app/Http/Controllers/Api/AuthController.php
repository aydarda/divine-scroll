<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // POST /api/v1/auth/register
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:60'],
            'email'       => ['required', 'email:rfc', 'max:190', 'unique:users,email'],
            'password'    => ['required', 'confirmed', Password::min(10)],
            'consent'     => ['accepted'],
            'device_name' => ['nullable', 'string', 'max:60'],
        ], [
            'consent.accepted' => 'Please accept the privacy policy to create an account.',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => strtolower($data['email']),
            'password' => $data['password'],
        ]);

        return response()->json($this->session($user, $data['device_name'] ?? 'mobile'), 201);
    }

    // POST /api/v1/auth/login
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'       => ['required', 'email'],
            'password'    => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:60'],
        ]);

        $user = User::where('email', strtolower($data['email']))->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['These details don’t match an account.']]);
        }

        if ($user->suspended_at) {
            throw ValidationException::withMessages(['email' => ['This account has been suspended.']]);
        }

        return response()->json($this->session($user, $data['device_name'] ?? 'mobile'));
    }

    // POST /api/v1/auth/logout: revokes only the token that made this request.
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['ok' => true]);
    }

    // GET /api/v1/me
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user'   => $this->user($user),
            'counts' => [
                'saved' => $user->bookmarkedPassages()->count(),
                'notes' => $user->notes()->count(),
            ],
        ]);
    }

    // DELETE /api/v1/me: permanently deletes the account, its saved passages, notes, and comments.
    public function destroy(Request $request)
    {
        $request->validate(['password' => ['required', 'string']]);

        $user = $request->user();

        if (! Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages(['password' => ['That password is not correct.']]);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['ok' => true]);
    }

    private function session(User $user, string $device): array
    {
        return [
            'token' => $user->createToken($device)->plainTextToken,
            'user'  => $this->user($user),
        ];
    }

    private function user(User $user): array
    {
        return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email];
    }
}
