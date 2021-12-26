<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\User;

use App\Transformers\Users\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     *  Handle Login Request
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $user = User::firstWhere('email', $request->email);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            if ($user) {
                $user = Auth::user();
                $user_data = fractal()
                    ->item($user)
                    ->transformWith(new UserTransformer())
                    ->includeRoles()
                    ->toArray();
                if (!$user->hasRole('super-admin')) {
                    return $this->ResponseApi("", __('messages.admin login not allowed'), 403);
                }
                $token = $user->createtoken('my-app')->accessToken;
                return $this->ResponseApi(__('messages.success login'), $user_data, 200, ['token' => $token]);
            }
        }
        return $this->ResponseApi(__('messages.admin failed login'), null, 401);
    }

}
