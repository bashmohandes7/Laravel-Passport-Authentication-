<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\RegisterRequest;
use App\Models\ResetPassword;
use App\Models\User;
use App\Transformers\Front\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Register new user and send code to active his email
     * @param RegisterRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *  todo token
     */
    public function register(RegisterRequest $request)
    {
        $email =$request->input('email');
        $code = Hash::make(Str::random(10)); //code hashed

        // create user and save code that sent by email to database storage
        $user = User::create([
            'name' => $request->input('name'),
            'password' => $request->input('password'),
            'email' => $email,
            'activation_token' => $code
        ]);
        if ($request->hasFile('avatar')) {
            $user->clearMediaCollection('avatars');
            $user->addMedia($request->avatar)->toMediaCollection('avatars');
        }
        // send code via email to activate user email
        $token = $user->createToken('my-app')->accessToken; // generate token
        Mail::send('emails.verify_account', ['code'=>$code], function(Message $message) use ($email) {
            $message->to($email);
            $message->subject('Confirm your account');
        });

        $fractal = fractal()
            ->item($user)
            ->transformWith(new UserTransformer())
            ->toArray();

        return $this->ResponseApi('Check your email to verify', $fractal, 200, ['token'=>$token]);
    }

    /**
     * Verify email by code that saved in database
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email', // to catch user by unique email
            'code' => 'required|exists:users,activation_token'
        ]);
        $user = User::where('email', $request->input('email'))->first();
        if (!$user) {
            return $this->ResponseApi('This User is not exists.', null, 404);
        }
        $token = $user->createToken('my-app')->accessToken; // generate token
        $user->update([
            'active' => 1,
            'activation_token' => '',
        ]);
        return $this->ResponseApi('Your Account has been Verified', $user, 200, ['token'=>$token]);
    }

    /**
     * check if account is active , if true login successfully
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only(['email', 'password']);
        $credentials['active'] = 1;
        if(!Auth::attempt($credentials)) {
            $this->ResponseApi('Unauthorized', null, 401);
        }
        $user = $request->user();
        $token = $user->createToken('my-app')->accessToken;
        $fractal = fractal()
            ->item($user)
            ->transformWith(new UserTransformer())
            ->toArray();
        return $this->ResponseApi('Login Successfully', $fractal, 200, ['token' => $token]);
    }

    /**
     * Forgot password 'Send code via email'
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function forgot(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);
        $email = $request->input('email');
        $user = User::where('email', $email)->first();
        if(!$user){
            return $this->responseApi("User Doesn't Exists");
        }
        $code  = hash::make(Str::random(10)); // hashed code
        ResetPassword::create([
            'email' => $email,
            'token' => $code
        ]);
        Mail::send('emails.reset_password', ['code'=>$code], function(Message $message) use ($email){
            $message->to($email);
            $message->subject('Reset Your Password');
        });
        return $this->responseApi('Check Your Email!');
    }

    /**
     * Reset password via token it sent by email
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|exists:password_resets,token',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $passwordReset = ResetPassword::where('token', $request->input('code'))->first();
        if(!$passwordReset){
            return $this->responseApi('Invalid Token!', null, 400);
        }
        $user = User::where('email', $request->input('email'))->first();
        if(!$user){
            return $this->responseApi("User doesn't Exists", null, 404);
        }
        $user->password = $request->input('password'); // update password
        $user->save();
        return $this->responseApi('Success');
    }
}
