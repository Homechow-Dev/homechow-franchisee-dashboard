<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Carbon\Carbon;

use function PHPUnit\Framework\isEmpty;

class LoginController extends BaseController
{
    public function __invoke()
    {
        request()->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required'],
        ]);

        /**
         * We are authenticating a request from our frontend.
         */
        if (EnsureFrontendRequestsAreStateful::fromFrontend(request())) {
            if(! $this->authenticateFrontend()){
            }
        }
        /**
         * We are authenticating a request from a 3rd party.
         */
        else {
            // Use token authentication.
            if (!Auth::attempt(request()->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Invalid login details'
                ], 401);
            }
            $user = User::where('email', request()['email'])->firstOrFail();
            $roles = $user->getRoleNames();
            $token= $user->createToken('api_token', ['api-access'], Carbon::now()->addMinutes(config('sanctum.ac_expiration')))->plainTextToken;

            $success['token'] =  $token;
            $success['name'] =  $user->name;
            // Roles & permission
            $success['role'] = $roles;
            return $this->sendResponse($success, 'User registered successfully.');
        }
    }

    private function authenticateFrontend()
    {
        if (!Auth::attempt(
                request()->only('email', 'password'),
                request()->boolean('remember')
            )) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }
    }
}