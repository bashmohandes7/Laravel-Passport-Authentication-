<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    // show profile if you are authenticated
    public function profile()
    {
        return $this->ResponseApi('', auth('api')->user());
    }
    /**
     * Change password
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string|exists:users,password',
            'new_password' => 'required|min:6|confirmed',
        ]);
        if ((Hash::check(request('old_password'), Auth::user()->password)) === false) {
            return $this->ResponseApi('Check your old password.', null, 400);
        } else if ((Hash::check(request('new_password'), Auth::user()->password)) === true) {
            return $this->ResponseApi('Please enter a password which is not similar then current password.', null, 400);
        }
        $user = User::where('id', auth()->user()->id)->first(); // catch user by id
        $user->password = $request->input('new_password');
        $user->save();
        return $this->ResponseApi('Password updated successfully.');
    }
    /**
     * Logout user, revoke token and delete it
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function logout()
    {
        auth('api')->user()->token()->revoke();
        auth('api')->user()->token()->delete();
        return $this->ResponseApi('Logged out');
    }
}
