<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller {
    
    public function __invoke()
    {
        print('hello i am the user controller');
        return UserResource::make(
            auth()->user()
        );
    }
}