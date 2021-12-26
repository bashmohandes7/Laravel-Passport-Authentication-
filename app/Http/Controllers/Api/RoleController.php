<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Transformers\Roles\RolesTransformer;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * RoleController constructor.
     */
    public function __construct()
    {
        $this->middleware('permission:role-list',   ['only' => ['index']]);
        $this->middleware('permission:role-create', ['only' => ['store']]);
        $this->middleware('permission:role-update', ['only' => ['update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    /**
     * Get all Roles, roles counts, Search by role name
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles=Role::when($request->search ,function($q) use ($request){
            $q->where('name', 'LIKE', "%{$request->search}%");
        });
        $count = $roles->count();
        $roles = $roles->orderBy('id', 'DESC')->get();
        $skip = ($request->has('skip')) ? $request->skip : 0;
        if ($request->has('skip')) {
            $roles = $roles->orderBy('id', 'DESC')->skip($skip)->take(10)->get();
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
            'permission' => 'required',
        ]);
        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

        $role = fractal()
            ->item($role)
            ->transformWith(new RolesTransformer())
            ->includePermissions()
            ->toArray();
        return $this->ResponseApi(__('messages.role create'), $role);
    }

    /**
     * Display the specified Role with Permissions.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
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
            'permission' => 'required',
        ]);
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();
        $role->syncPermissions($request->input('permission'));
        $role = fractal()
            ->item($role)
            ->transformWith(new RolesTransformer())
            ->includePermissions()
            ->toArray();
        return $this->ResponseApi(__('messages.role update'), $role);
    }

    public function destroy($id)
    {
        Role::where('id', $id)->delete();
        return $this->ResponseApi(__('messages. role delete'));
    }
}
