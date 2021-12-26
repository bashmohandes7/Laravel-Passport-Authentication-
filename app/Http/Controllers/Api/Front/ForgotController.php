<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\ForgotRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotController extends Controller
{
    /**
     * first Check if user email exists
     * create code
     *  Send the code via Email
     * @param ForgotRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function forgot(ForgotRequest $request)
    {
        $email = $request->input('email');
        if(User::where('email', $email)->doesntExist()){
            return $this->responseApi("User Doesn't Exists");
        }
        $token  = Str::random(10);

        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token
        ]);
        Mail::send('emails.forgot_password', ['token'=>$token], function(Message $message) use ($email){
            $message->to($email);
            $message->subject('Reset You Password');
        });
        return $this->responseApi('Check Your Email!');
    }
}
