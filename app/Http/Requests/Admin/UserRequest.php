<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    protected function onCreate() {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
            'roles' => 'required'
        ];
    }

    protected function onUpdate() {
        return [
            'name' => 'string',
            'email' => 'email|unique:users,email',
            'password' => 'string|min:6',
            'password_confirmation'=> 'string|same:password',
            'roles' => 'nullable'
        ];
    }

    public function rules() {
        return request()->isMethod('put') || request()->isMethod('patch') ?
            $this->onUpdate() : $this->onCreate();
    }
}
