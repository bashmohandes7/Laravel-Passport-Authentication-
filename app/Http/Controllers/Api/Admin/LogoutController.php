<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Handling Admin Logout ''
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function logout () {
        auth('admin')->user()->token()->revoke();
        auth('admin')->user()->token()->delete();
        return $this->responseApi(__('messages.admin logout'));
    }
}
