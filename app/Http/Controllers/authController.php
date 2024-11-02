<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class authController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function register(Request $request)
    {
        $dataUser = new User();

        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }

        $dataUser->name = $request->name;
        $dataUser->email = $request->email;
        $dataUser->password = bcrypt($request->password);
        $dataUser->save();

        return response()->json([
            'status' => true,
            'message' => 'Success',
        ], 200);
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => true,
                'message' => 'Email atau password salah'
            ], 401);
        }

        $dataUser = User::where('email', $request->email)->first();
        $role = Role::join("user_role", "user_role.role_id", "=", "roles.id")
            ->join("users", "users.id", "=", "user_role.role_id")
            ->where("user_id", $dataUser->id)
            ->pluck("roles.role_name")->toArray();

        if (empty($role)) {
            $role = ["*"];
        }

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'token' => $dataUser->createToken('API Token', $role)->plainTextToken
        ], 200);
    }
}
