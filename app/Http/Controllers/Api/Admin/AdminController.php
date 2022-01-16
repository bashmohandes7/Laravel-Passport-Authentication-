<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Admin;
use App\Models\User;
use App\Transformers\Admin\AdminTransformer;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     *  Get All Admins, admins count, admins roles, admins Permissions
     */
    public function index(Request $request)
    {

        $skip = $request->skip ?? 0;
        // Search by admin email or admin name
        $admins = Admin::when($request->q, function ($q) use ($request) {
            $q->where('email', 'LIKE', "%{$request->q}%")
                ->orWhere('name', 'LIKE', "%{$request->q}%");
        // check by admin roles
        })->when($request->role, function ($q) use ($request) {
            $q->whereRelation('roles', 'name', $request->role);
        });
        $count = $admins->count();
        $admins =  $skip !== 0 ? $admins->skip($skip)->take(10) : $admins;
        $admins = fractal()
            ->collection($admins->orderBy('created_at', 'DESC')->get())
            ->transformWith(new AdminTransformer())
            ->includeRoles()
            ->includePermissions()
            ->toArray();
        return $this->ResponseApi("", $admins, 200, ['count' => $count]);
    }

    /**
     *  Create New Admin
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(UserRequest $request)
    {
        $admin = Admin::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password'=> $request->input('password'),
        ]);

        if ($request->has('roles')) {
            $admin->syncRoles($request->input('roles'));
        }
        $fractal = fractal()
            ->item($admin)
            ->transformWith(new AdminTransformer())
            ->includeRoles()
            ->includePermissions()
            ->toArray();
        return $this->ResponseApi('Admin Created Successfully', $fractal);
    }

    /**  Display the specified admin with Roles
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show($id)
    {
        $fractal = fractal()
            ->item(Admin::findOrFail($id))
            ->transformWith(new AdminTransformer())
            ->includeRoles()
            ->includePermissions()
            ->toArray();
        return $this->ResponseApi("", $fractal);
    }

    /**
     * Update Specified Admin
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(UserRequest $request, $id)
    {
        $admin = Admin::findOrFail($id);
        $admin->update([
            'name' => $request->input('name') ?? $admin->name,
            'email' => $request->input('email') ?? $admin->email,
            'password' => $request->input('password') ?? $admin->password,
        ]);
        if ($request->has('roles')) {
            $admin->syncRoles($request->input('roles'));
        }
        $fractal = fractal()
            ->item($admin)
            ->transformWith(new AdminTransformer())
            ->includeRoles()
            ->includePermissions()
            ->toArray();
        return $this->ResponseApi('Admin Updated Successfully', $fractal);
    }

    /**
     * check admin authenticated
     * revoke his token and delete it
     * delete admin from database
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();
        return $this->responseApi('Admin Deleted Successfully');
    }
}
