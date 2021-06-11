<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $tenant = Tenant::create([
            'plan' => 'free'
        ]);

        $tenant->domains()->create(['domain' => $request->name . '.beehive-app-api.test']);

        $user = $tenant->run(function () use ($request) {
            return User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        });

        return response()
            ->json([
                'user' => $user,
                'status' => 'success',
                'message' => 'Successfully registered',
                'code' => JsonResponse::HTTP_OK,
            ])
            ->setStatusCode(200);
    }
}
