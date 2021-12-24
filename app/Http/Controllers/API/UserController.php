<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Fortify\Rules\Password;
use App\Models\User;
use App\Helpers\ResponseFormatter;
use \Auth;
use \Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try{
            $request->validate([
                'name' => 'required|string|max:255|min:3',
                'email' => 'required|string|max:255|email|unique:users',
                'phone' => 'nullable|string|max:20',
                'username' => 'required|string|max:255|unique:users',
                'password' => ['required', 'string', 'max:255', new Password],
            ]);


            $data = $request->all();
            $data['password'] = bcrypt($request->input('password'));
            User::create($data);
            $user = User::where('email', $request->input('email'))->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user, 
            ], 'Register user berhasil!');
        } catch (Exeption $error) {
            return ResponseFormatter::error([
                'error' => $error
            ], 'Register user gagal!', 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)){
                return ResponseFormatter::error(['message' => 'Email atau password salah'], 'Login Failed!', 500);
            } else {
                $user = User::where('email', $request->email)->first();
                if(!Hash::check($request->password, $user->password, [])) {
                    throw new \Exception('Invalid Credentials');
                }

                $tokenResult = $user->createToken('authToken')->plainTextToken;

                return ResponseFormatter::success([
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                    'user' => $user, 
                ], 'Login user berhasil!');
            }
        } catch (Exception $error) {
            return ResponseFormatter::error(['message' => 'Email atau password salah', 'error' => $error], 'Login Failed!', 500);
        }
    }

    public function getUser(Request $request)
    {
        return ResponseFormatter::success($request->user(), 'Data user berhasil di load!');
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'email' => 'email|string|unique:users,email,' .$user->id,
                'name' => 'string|min:3|max:255',
                'username' => 'string|max:255|unique:users,username,' .$user->id,
                'phone' => 'string|max:20'
            ]);
            $user->update($request->all());
            return ResponseFormatter::success($user, 'Data user berhasil diupdate!');
        } catch (Exception $error) {
            return ResponseFormatter::error(['error' => $error], 'Data user gagal diupdate!', 500);
        }
    }

    public function logout(Request $request) {
        $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success($token, 'Token revoked');
    }
}
