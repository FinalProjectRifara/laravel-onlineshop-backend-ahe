<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //validate the request
        $validated = $request->validate([
            'name'  => 'required|max:100',
            'email' => 'required|email|unique:users', 'max:100',
            'phone' => 'required|max:18',
            'roles' => 'required',
            'password' => 'required',
            'address' => 'required|max:200',
        ]);

        // password encryption
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => "Logged out",], 200);
    }

    public function  login(Request $request)
    {
        // Validate the request...
        $validated = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        // if (!$user || !Hash::check($validated['password'], $user->password)) {
        //     return response()->json([
        //         'message' => 'Invalid credentials!'
        //     ], 401);
        // }

        if (!$user) {
            return response()->json([
                'message' => 'User not found.'
            ], 401);
        }

        if (!Hash::check($validated['password'], $user->password)) {
            return  response()->json([
                'message'=> 'Password mismatched / invalid password'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => $user,
        ], 200);
    }

    // Update firebase cloud messaging (fcm_id)
    public function updateFcmId(Request $request){
        // Validate the request
        $validated = $request->validate([
            'fcm_id' => 'required'
        ]);

        $user = $request->user();
        $user -> fcm_id = $validated['fcm_id'];
        $user->save();

        return response()->json([
            'message' => 'FCM ID updated',
        ], 200);
    }

    // public function updateData(Request $request, $id)
    // {
    //     // $user = Auth::user();

    //     $user = Auth::user()($id);

    //     $validator = Validator::make($request->all(), [
    //         'name' => 'sometimes|string|max:255',
    //         'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
    //         'phone' => 'sometimes|string|max:18',
    //         'address' => 'sometimes|string|max:200',
    //         'roles' => 'sometimes|string',
    //         // Tambahkan validasi lainnya sesuai kebutuhan
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     // $user->update($data);
    //     $user->update($request->only('name', 'email', 'phone', 'address', 'roles'));

    //     Log::info('User after update', ['user' => $user]);

    //     return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    // }
}
