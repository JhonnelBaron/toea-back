<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Nominee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller implements HasMiddleware
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
        public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['login','register', 'logout']),
        ];
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(JWTAuth::user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        JWTAuth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(JWTAuth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }

    public function register(Request $request)
    {
    $validator = Validator::make($request->all(), [
        'user_type' => 'required|string',
        'first_name' => 'nullable|string|max:255',
        'last_name' => 'nullable|string|max:255',
        'designation' => 'nullable|string',
        'position' => 'nullable|string',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'nominee_type' => 'nullable|in:BRO,GP,BTI',
        'nominee_category' => 'nullable|in:small,medium,large,ptc-dtc,rtc-stc,tas',
        'region' => 'nullable|string|max:255',
        'province' => 'nullable|string|max:255',
        'nominee_name' => 'nullable|string|max:65535',
        'status' => 'nullable', // Assuming status can be one of these values
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $user = User::create([
        'user_type' => $request->input('user_type'),
        'first_name' => $request->input('first_name'),
        'last_name' => $request->input('last_name'),
        'designation' => $request->input('designation'),
        'position' => $request->input('position'),
        'first_name' => $request->input('first_name'),
        'last_name' => $request->input('last_name'),
        'email' => $request->input('email'),
        'password' => Hash::make($request->input('password')),
    ]);

    if ($request->filled('nominee_type')) {
        Nominee::create([
            'user_id' => $user->id,
            'nominee_type' => $request->input('nominee_type'),
            'nominee_category' => $request->input('nominee_category'),
            'region' => $request->input('region'),
            'province' => $request->input('province'),
            'nominee_name' => $request->input('nominee_name'),
            'status' => $request->input('status'),
        ]);
    }

    $token = JWTAuth::fromUser($user);

    return response()->json([
        'message' => 'User registered successfully',
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => JWTAuth::factory()->getTTL() * 60
    ], 201);
}

}