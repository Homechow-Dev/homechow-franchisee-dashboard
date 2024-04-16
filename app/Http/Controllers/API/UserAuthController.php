<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Account;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserAuthController extends BaseController {
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)  {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        
   
        // $input = $request->all();
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('api_token', ['api-access'], Carbon::now()->addMinutes(config('sanctum.ac_expiration')))->plainTextToken;
        $refreshToken = $user->createToken('refresh_token', ['api:token-refresh'], Carbon::now()->addMinutes(config('sanctum.rt_expiration')))->plainTextToken;
        
        $Acct = Account::create([
            'Name' => $request->name,
            'user_id' => $user->id,
        ]);
        $success['token'] =  $token;
        $success['name'] =  $user->name;
        $success['refresh_token'] = $refreshToken;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request) {

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }
        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('api_token')->plainTextToken;
        $success['token'] =  $token; 
        $success['name'] =  $user->name;
        return $this->sendResponse($success, 'User login successfully.');
    }

    public function logout(){
        auth()->user()->api_token->delete();
    
        return response()->json([
          "message"=>"logged out"
        ]);
    }

    public function refreshToken(Request $request) {
            $token = $request->user()->createToken('api_token', ['api-access'], Carbon::now()->addMinutes(config('sanctum.ac_expiration')))->plainTextToken;
            $refreshToken = $request->user->createToken('refresh_token', ['api:token-refresh'], Carbon::now()->addMinutes(config('sanctum.rt_expiration')))->plainTextToken;
            $success['token'] =  $token;
            $success['refresh_token'] = $refreshToken;
            return $this->sendResponse($success, 'User token refreshed.');
    }
}
