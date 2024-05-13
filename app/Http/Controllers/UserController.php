<?php

namespace App\Http\Controllers;

use App\helpers\ResponseFormatter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\error;

class UserController extends Controller
{

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required',
            ]);

            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error([
                    'message' => 'Incorrect email or password',
                ], 'Authentication failed', 500);
            }

            $user = User::where('email', $request->email)->first();

            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Incorrect email or password');
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Login success');
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Login failed',
                'error' => $error->errors(),
            ], 'Login failed', 500);
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Login failed',
                'error' => $error->getMessage(),
            ], 'Login failed', 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users,username'],
                'email' => ['required', 'string', 'max:255', 'unique:users,email'],
                'phone' => ['nullable', 'string', 'max:255', 'unique:users,phone'],
                'password' => ['required', 'string', new Password(8)],
            ]);

            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)->first();

            $token = $user->createToken('auth_token')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 'Register success');
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Registration failed',
                'error' => $error->errors(),
            ], 'Registration failed', 500);
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Registration failed',
                'error' => $error->getMessage(),
            ], 'Registration failed', 500);
        }
    }

    public function fetch(Request $request)
    {
        return ResponseFormatter::success($request->user(), 'User profile fetched successfully');
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'name' => ['string', 'max:255'],
                'username' => ['string', 'max:255', 'unique:users,username'],
                'email' => ['string', 'max:255', 'unique:users,email'],
                'phone' => ['string', 'max:255', 'unique:users,phone'],
            ]);

            $data = $request->all();

            $user = Auth::user();
            $user->update($data);

            return ResponseFormatter::success([
                'user' => $user,
                'message' => 'User profile updated successfully'
            ]);
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'User profile update failed',
                'error' => $error->errors(),
            ], 'User profile update failed', 500);
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'User profile update failed',
                'error' => $error->getMessage(),
            ], 'User profile update failed', 500);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Logged out successfully');
    }
}
