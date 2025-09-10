<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

final class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

<<<<<<< Updated upstream
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'user' => $user,
        ]);
=======
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
>>>>>>> Stashed changes
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

<<<<<<< Updated upstream
        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $request->session()->regenerate();
=======
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
>>>>>>> Stashed changes

        return response()->json([
            'user' => Auth::user(),
        ]);
    }

    public function logout(Request $request)
    {
<<<<<<< Updated upstream
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
=======
        if ($request->user()->currentAccessToken() instanceof PersonalAccessToken) {
            $request->user()->currentAccessToken()->delete();
        } else {
            auth()->guard('web')->logout();
        }

        return response()->json([
            'message' => 'Logout realizado com sucesso'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
>>>>>>> Stashed changes
    }
}
