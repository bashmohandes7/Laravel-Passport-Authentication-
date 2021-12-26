<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Transformers\Users\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware('permission:user-list', ['only' => ['index']]);
        $this->middleware('permission:user-create', ['only' => ['store']]);
        $this->middleware('permission:user-update', ['only' => ['update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /**
     * Get All Users, users count, users roles, users Permissions
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::when($request->search, function ($q) use ($request) {
            $q->where('email', 'LIKE', "%{$request->search}%")
                ->orWhere('name', 'LIKE', "%{$request->search}%");
        })->when($request->role, function ($q) use ($request) {
            $q->whereRelation('roles', 'name', $request->role);
        });
        $count = $users->count();
        $skip = ($request->has('skip')) ? $request->skip : 0;
        if ($request->has('skip')) {
            $users = $users->orderBy('created_at', 'DESC')->skip($skip)->take(10)->get();
        }else{
            $users = $users->orderBy('created_at', 'DESC')->get();
        }
        $users = fractal()
            ->collection($users)
            ->transformWith(new UserTransformer())
            ->includeRoles()
            ->toArray();
        return $this->ResponseApi("", $users, 200, ['count' => $count]);
    }

    /**
     *  Create New User
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $user->assignRole($request->input('roles'));
        $user = fractal()
            ->item($user)
            ->transformWith(new UserTransformer())
            ->toArray();
        return $this->ResponseApi(__('messages.user create'), $user);
    }

    /**  Display the specified User with Roles
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $user = fractal()
            ->item($user)
            ->transformWith(new UserTransformer())
            ->includeRoles()
            ->toArray();
        return $this->ResponseApi("", $user);
    }

    /**
     * Update Specified User
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            Arr::except($input, array('password'));
        }
        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();

        $user->assignRole($request->input('roles'));

        $user = fractal()
            ->item($user)
            ->transformWith(new UserTransformer())
            ->includeRoles()
            ->toArray();
        return $this->ResponseApi(__('messages.user update'), $user);
    }

    /**
     * Check if user exists first,
     *  if yes Revoke his token
     *  delete his token
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('id', $id)->first();
        $user->token()->revoke();
        $user->token()->delete();
        return $this->responseApi('User Deleted Successfully');
    }
}
