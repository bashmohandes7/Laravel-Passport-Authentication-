<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\Admin;
use App\Models\User;

use App\Transformers\Admin\AdminTransformer;
use App\Transformers\Front\UserTransformer;
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

        $admin = Admin::where('email', $request->input('email'))->first();
        if ($admin) {
                if (!$admin->hasRole('super-admin')) {
                    return $this->ResponseApi("login not Allowed", null,403);
                }
                $token = $admin->createToken('my-app')->accessToken;
                $admin_data = fractal()
                ->item($admin)
                ->transformWith(new AdminTransformer())
                ->includeRoles()
                ->includePermissions()
                ->toArray();
                return $this->ResponseApi('login Successfully', $admin_data, 200, ['token' => $token]);
        }
         return $this->ResponseApi('Failed Login', null, 401);
    }

    /**
     * show authenticated admin profile
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function profile()
    {
        $fractal = fractal()
            ->item(auth('admin')->user())
            ->transformWith(new AdminTransformer())
            ->includeRoles()
            ->includePermissions()
            ->toArray();
        return $this->responseApi('', $fractal);
    }
}
