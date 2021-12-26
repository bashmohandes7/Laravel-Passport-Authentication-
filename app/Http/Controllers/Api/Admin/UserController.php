<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
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
    public function store(UserRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password'=> $request->input('password')
        ]);
        $user->assignRole($request->input('roles'));
        $fractal = fractal()
            ->item($user)
            ->transformWith(new UserTransformer())
            ->toArray();
        return $this->ResponseApi(__('messages.user create'), $fractal);
    }

    /**  Display the specified User with Roles
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $fractal = fractal()
            ->item($user)
            ->transformWith(new UserTransformer())
            ->includeRoles()
            ->toArray();
        return $this->ResponseApi("", $fractal);
    }

    /**
     * Update Specified User
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(UserRequest $request, $id)
    {


        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->input('name') ?? $user->name,
            'email' => $request->input('email') ?? $user->email,
            'password' => $request->input('password') ?? $user->password,
            'roles' => $request->input('roles') ?? $user->roles
        ]);
        DB::table('model_has_roles')->where('model_id', $id)->delete();

        $user->assignRole($request->input('roles'));

        $fractal = fractal()
            ->item($user)
            ->transformWith(new UserTransformer())
            ->includeRoles()
            ->toArray();
        return $this->ResponseApi(__('messages.user update'), $fractal);
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
        User::where('id', $id)->delete();
        return $this->responseApi('User Deleted Successfully');
    }
    public function profile()
    {
        $fractal = fractal()
            ->item(auth()->user())
            ->transformWith(new UserTransformer())
            ->includeRoles()
            ->toArray();
        return $this->responseApi('', $fractal);
    }
}
