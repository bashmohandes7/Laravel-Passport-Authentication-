<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Transformers\Admin\Permissions\PermissionTransformer;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $skip = ($request->has('skip')) ? $request->skip : 0;
        // search by permission name
        $permissions=Permission::when($request->q ,function($q) use ($request){
            $q->where('name', 'LIKE', "%{$request->q}%");
         // filter by role name
        })->when($request->role ,function($q) use ($request){
            $q->whereRelation('roles', 'name', $request->role);
        });
        $count = $permissions->count();

        if ($request->has('skip')) {
            $permissions = $permissions->orderBy('id', 'DESC')->skip($skip)->take(10)->get();
        }else{
            $permissions = $permissions->orderBy('id', 'DESC')->get();
        }
        $permissions = fractal()
            ->collection($permissions)
            ->transformWith(new PermissionTransformer())
            ->toArray();
        return $this->ResponseApi("", $permissions, 200,['count' => $count]);
    }
}
