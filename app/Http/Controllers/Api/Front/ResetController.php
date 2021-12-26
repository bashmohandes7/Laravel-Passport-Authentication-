<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\ResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetController extends Controller
{
    /**
     * check token
     * Add new  Password
     * @param ResetRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function reset(ResetRequest $request)
    {
        $token = $request->input('token');
        $passwordReset = DB::table('password_resets')->where('token', $token)->first();
        if(!$passwordReset){
            return $this->responseApi('Invalid Token!', null, 400);
        }
        $user = User::where('email', $passwordReset->email)->first();
        if(!$user){
            return $this->responseApi('User doesn\'t Exists', null, 404);
        }
        $user->password = $request->input('password');
        $user->save();
        return $this->responseApi('Success');
    }
}
