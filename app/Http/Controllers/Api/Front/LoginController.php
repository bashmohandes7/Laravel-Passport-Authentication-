<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\LoginRequest;
use App\Transformers\Users\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {

        if(Auth::attempt($request->only('email', 'password'))){
            $user = Auth::user();
            $token = $user->createToken('app')->accessToken;

            $fractal = fractal()
                ->item($user)
                ->transformWith(new UserTransformer())
                ->toArray();
            return $this->ResponseApi('Success', $fractal, 200, ['token' => $token]);
        }else{
            return $this->ResponseApi('invalid User');
        }
    }
}
