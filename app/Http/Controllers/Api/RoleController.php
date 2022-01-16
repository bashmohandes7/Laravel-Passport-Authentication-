<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Transformers\Admin\Roles\RolesTransformer;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Get all Roles, roles counts, Search by role name
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $skip = ($request->has('skip')) ? $request->skip : 0;
        // search by role name
        $roles=Role::when($request->q ,function($q) use ($request){
            $q->where('name', 'LIKE', "%{$request->q}%");
        });
        $count = $roles->count();
        if ($request->has('skip')) {
            $roles = $roles->orderBy('id', 'DESC')->skip($skip)->take(10)->get();
        }else{
            $roles = $roles->orderBy('id', 'DESC')->get();
        }
        $roles = fractal()
            ->collection($roles)
            ->transformWith(new RolesTransformer())
            ->includePermissions()
            ->toArray();
        return $this->ResponseApi("", $roles, 200,['count' => $count]);
    }
    /**
     * Store a newly created Role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required|array',
            'permission.*' => 'integer'
        ]);
        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));
        $role = fractal()
            ->item($role)
            ->transformWith(new RolesTransformer())
            ->includePermissions()
            ->toArray();
        return $this->ResponseApi('Role Created Successfully', $role);
    }

    /**
     * Display the specified Role with Permissions.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::findOrFail($id);
        $role = fractal()
            ->item($role)
            ->transformWith(new RolesTransformer())
            ->includePermissions()
            ->toArray();
        return $this->ResponseApi("", $role);
    }


    /**
     * Update the specified Role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'sometimes|nullable|array',
            'permission.*' => 'integer'
        ]);
        $role = Role::findOrFail($id);
        $role->name = $request->input('name');
        $role->syncPermissions($request->input('permission'));
        $role->save();
        $fractal = fractal()
            ->item($role)
            ->transformWith(new RolesTransformer())
            ->includePermissions()
            ->toArray();
        return $this->ResponseApi('Role Updated Successfully', $fractal);
    }

    /**
     * Check if that role is exists and delete it
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return $this->ResponseApi('Role Deleted Successfully');
    }
}
