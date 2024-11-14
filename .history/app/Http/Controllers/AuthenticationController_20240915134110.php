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
    // Login Method
    public function login(Request $request)
    {
        // Validate the input
        $validator = $this->validateLogin($request);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if the user exists and the password is valid
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid credentials. Please check your email or password.', 401);
        }

        // Create a new API token
        $token = $user->createToken($request->device_name)->plainTextToken;

        // Return success response with token
        return $this->successResponse('Login successful', [
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    // Signup Method
    public function signup(Request $request)
    {
        $validator = $this->validateSignup($request);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $data = $validator->validated();

        // Generate a default username if not provided
        $data['username'] = $data['username'] ?? $this->generateDefaultUsername($data['name']);

        // Create the user
        $user = User::create([
            'avatar_id' => $data['avatar_id'],
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'] ?? 1, // Default role_id to 1 if not provided
        ]);

        // Return success response
        return $this->successResponse('User created successfully', new UserResource($user), 201);
    }

    // Logout Method
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse('You have been logged out');
    }

    // Get current logged-in user
    public function me(Request $request)
    {
        return $this->successResponse('User details retrieved successfully', new UserResource(Auth::user()->load('role', 'avatar')));
    }

    // Update user method
    public function update(Request $request, $id)
    {
        $validator = $this->validateUpdate($request, $id);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $user = User::findOrFail($id);
        $user->update($validator->validated());

        return $this->successResponse('User updated successfully', new UserResource($user));
    }


    // Delete user method
    public function delete($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }

        $user->delete();

        return $this->successResponse('User deleted successfully');
    }

    // Helper Method: Generate default username
    private function generateDefaultUsername($name)
    {
        return strtolower(Str::slug($name . Str::random(4)));
    }

    // Helper Method: Validate login
    private function validateLogin(Request $request)
    {
        return Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'required|string',
        ]);
    }

    // Helper Method: Validate signup
    private function validateSignup(Request $request)
    {
        return Validator::make($request->all(), [
            'avatar_id' => 'required',
            'name' => 'required|max:50',
            'username' => 'nullable|unique:users',
            'email' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);
    }

    // Helper Method: Validate update
    private function validateUpdate(Request $request, $id)
    {
        return Validator::make($request->all(), [
            'avatar_id' => 'required',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
        ]);
    }

    // Helper Method: Success response
    private function successResponse($message, $data = null, $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    // Helper Method: Error response
    private function errorResponse($message, $status = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null,
        ], $status);
    }
}
