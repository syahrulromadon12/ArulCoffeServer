<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public function login(request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Your email or password is wrong'],
            ]);
        }
    
        return $user->createToken($request->device_name)->plainTextToken;
    }

    public function signup(Request $request) {
        $validator = Validator::make($request->all(), [
            'avatar_id' => 'required',
            'name' => 'required|max:50',
            'username' => 'nullable|unique:users',
            'email' => 'required|unique:users',
            'password' => 'required'
        ]);
    
        if ($validator->fails()) {
            $errors = $validator->errors();
            $errorMessages = $errors->all();
            return response()->json(['errors' => implode(' ', $errorMessages)], 422);
        }
    
        // Generate a default username if not provided or if it already exists
        $data = $validator->validated();
        if (empty($data['username'])) {
            $data['username'] = $this->generateDefaultUsername($data['name']);
        }
    
        // Proceed with creating the user
        $user = User::create([
            'avatar_id' => $data['avatar_id'],
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'] ?? 1, 
        ]);
    
        // Return success response
        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }
    
    private function generateDefaultUsername($name) {
        $randomText = Str::random(6);
        return strtolower(Str::slug($name . $randomText));
    }
    

    public function logout(request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'You are Loged out'], 200);
    }

    public function me(request $request){
        return new UserResource(Auth::user()->load('role', 'avatar'));
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'avatar_id' => 'required',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cari user berdasarkan id
        $user = User::findOrFail($id);

        // Update seluruh field
        $user->avatar_id = $request->avatar_id;
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;

        $user->save();

        return response()->json(['message' => 'updated has been successful'], 200);
    }

    public function delete($id){
        // Cari user berdasarkan ID
    $user = User::find($id);

    // Cek apakah user ada
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Hapus user
    $user->delete();

    return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
