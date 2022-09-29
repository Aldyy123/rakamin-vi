<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required|min:4',
                'email' => 'required|email',
                'password' => 'required|min:8',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $token = $user->createToken('accessToken')->accessToken;
            return response()->json(['token' => $token], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            if ($th->getCode() === '23000') {
                return response()->json(['error' => 'Email has been exist'], Response::HTTP_UNAUTHORIZED);
            }
            throw $th;
        }
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('accessToken')->accessToken;
            return response()->json(['token' => $token], Response::HTTP_CREATED);
        } else {
            return response()->json(['error' => 'Unauthorised'], Response::HTTP_UNAUTHORIZED);
        }
    }
}
