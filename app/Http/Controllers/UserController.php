<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json([
            'users' => $users,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!$user)
        {
            return response()->json([
                'message' => 'No users with this ID found'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'first_name' => "required|string|max:255",
            'last_name' => "required|string|max:255",
        ]);
        if ($validator->fails())
        {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->save();


        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (!$user)
        {
            return response()->json([
                'message' => 'No users with this ID found'
            ], 404);
        }
        $user->delete();
        return response()->json([
            'message' => 'User delete successfully'
        ], 204);
    }

    /**
     * register - user to register
     */
    public function register(Request $request)
    {
        if(User::where('email', $request->email)->exits())
        {
            return response()->json([
                'message' =>  'User with this email already exits'
            ], 409);
        }
        $validator = Validator::make($request->all(), [
            'first_name' => "required|string|max:255",
            'last_name' => "required|string|max:255",
            'email' => "required|string|email|unique:users",
            'password' => 'required|string|min:8|confirmed',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails())
        {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $profilePath = null;
        if ($request->hasFile('profile')){
            $profilePath = $request->file('profile')->store('profiles', 'public');
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile' => $profilePath,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'register succesfully',
        ], 201);
    }

    /**
     * login - function for the user to login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password))
        {
            return response()->json([
                'message' => 'Invalid login credential'
            ], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Login successful',
            'token' => $token
        ], 200);
    }

    /**
     * logout - user to logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logout successful'
        ], 200);
    }
}
