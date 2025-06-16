<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\Nominee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
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
            new Middleware('auth:api', except: ['login','register', 'logout', 'verifyOtp', 'resendOtp', 'sendResetLinkEmail', 'passwordReset', 'showResetForm', 'resetPassword']),
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

        $user = User::where('email', $credentials['email'])->first();
        if (! $user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Check if email is verified
        if (is_null($user->email_verified_at)) {
            return response()->json(['error' => 'Please verify your email address first.'], 403);
        }

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
        JWTAuth::invalidate(JWTAuth::getToken());

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
        'user_type' => 'nominee',
        'first_name' => $request->input('first_name'),
        'last_name' => $request->input('last_name'),
        'designation' => $request->input('designation'),
        'position' => $request->input('position'),
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

    // $token = JWTAuth::fromUser($user);

    // return response()->json([
    //     'message' => 'User registered successfully',
    //     'access_token' => $token,
    //     'token_type' => 'bearer',
    //     'expires_in' => JWTAuth::factory()->getTTL() * 60
    // ], 201);
        // Generate 6-digit OTP
    $otp = rand(100000, 999999);

    // Store OTP in cache for 10 minutes (adjust time if needed)
    Cache::put('otp_' . $user->email, $otp, now()->addMinutes(10));

    // Send OTP email
    Mail::to($user->email)->send(new OtpMail($otp));

    // Do NOT generate token or login yet — wait until verification is complete

    return response()->json([
        'message' => 'User registered successfully. OTP sent to email.',
        'verify_email' => $user->email,
    ], 201);
}

public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|digits:6',
    ]);

    $cachedOtp = Cache::get('otp_' . $request->email);

    if (!$cachedOtp) {
        return response()->json(['message' => 'OTP expired or not found.'], 400);
    }

    if ($cachedOtp != $request->otp) {
        return response()->json(['message' => 'Invalid OTP.'], 400);
    }

    // OTP is correct — mark email as verified
    $user = User::where('email', $request->email)->first();

    if ($user) {
        $user->email_verified_at = now();
        $user->save();

        Cache::forget('otp_' . $request->email);

        return response()->json(['message' => 'Email verified successfully.'], 200);
    }

    return response()->json(['message' => 'User not found.'], 404);
}
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Generate new OTP
        $otp = rand(100000, 999999);

        // Store new OTP in cache
        Cache::put('otp_' . $user->email, $otp, now()->addMinutes(10));

        // Send OTP email
        Mail::to($user->email)->send(new OtpMail($otp));

        return response()->json(['message' => 'OTP resent successfully.'], 200);
    }

      public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The email address is invalid or not registered.',
            ], 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Reset link sent to your email.']);
        } else {
            return response()->json([
                'message' => 'Unable to send reset link. Please try again later.',
            ], 500);
        }
        Log::info('Reset status:', ['status' => $status]);
    }

        public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
    
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );
    
        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successful'], 200);
        }
    
        return response()->json(['message' => 'Failed to reset password'], 400);
    }

    public function showResetForm(Request $request)
    {
        // Get token and email from the query params
        $token = $request->query('token');
        $email = $request->query('email');
    
        // If token or email is missing, return error message
        if (!$token || !$email) {
            return response()->json(['message' => 'Invalid or missing token.'], 400);
        }
    
        // Optionally, check if the token is valid by verifying it in the password_resets table
        // For now, Laravel automatically handles token validation when resetting the password
    
        return response()->json([
            'token' => $token,
            'email' => $email,
        ], 200);
    }

    

}