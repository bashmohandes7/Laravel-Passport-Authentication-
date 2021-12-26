<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Handling Admin Logout
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function logout () {
        auth()->user()->token()->revoke();
        auth()->user()->token()->delete();
        return $this->responseApi(__('messages.admin logout'));
    }
}
