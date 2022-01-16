<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Transformers\Users\UserTransformer;
use Illuminate\Http\Request;

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
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *  Get All Users, users count, users roles, users Permissions
     */
    public function index(Request $request)
    {

        $skip = $request->skip ?? 0;

        $users = User::when($request->q, function ($q) use ($request) {
            $q->where('email', 'LIKE', "%{$request->q}%")
                ->orWhere('name', 'LIKE', "%{$request->q}%");
        })->when($request->role, function ($q) use ($request) {
            $q->whereRelation('roles', 'name', $request->role);
        });
        $count = $users->count();
        $users =  $skip !== 0 ? $users->skip($skip)->take(10) : $users;
        $users = fractal()
            ->collection($users->orderBy('created_at', 'DESC')->get())
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
        // enhance
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password'=> $request->input('password'),
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->input('roles'));
        }
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
        $fractal = fractal()
            ->item(User::findOrFail($id))
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

        // enhance
        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->input('name') ?? $user->name,
            'email' => $request->input('email') ?? $user->email,
            'password' => $request->input('password') ?? $user->password,
        ]);
        if ($request->has('roles')) {
            $user->syncRoles($request->input('roles'));
        }

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
       $user = User::findOrFail($id);
       $user->delete();
        return $this->responseApi('User Deleted Successfully');
    }
    public function profile()
    {
        $fractal = fractal()
            ->item(auth('api')->user())
            ->transformWith(new UserTransformer())
            ->includeRoles()
            ->toArray();
        return $this->responseApi('', $fractal);
    }
}
