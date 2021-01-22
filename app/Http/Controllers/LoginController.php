<?php

namespace App\Http\Controllers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle an authentication
     *
     * @param Request $request
     * @return JsonResponse|object
     * @throws AuthenticationException
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            return response()
                ->json([
                    'status' => 'success',
                    'message' => 'Successfully logged in.',
                    'code' => JsonResponse::HTTP_OK,
                    'user' => Auth::user()
                    ])
                ->setStatusCode(200);
        }

        throw new AuthenticationException('Provided credentials are incorrect.');
    }

    /**
     * Handle the logging out of a user
     *
     * @param Request $request
     * @return JsonResponse|object
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out.',
            'code' => JsonResponse::HTTP_OK,
        ])
            ->setStatusCode(200);
    }
}
