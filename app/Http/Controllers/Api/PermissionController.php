<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Transformers\Permissions\PermissionTransformer;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:permission-list', ['only' => ['index']]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $permissions=Permission::when($request->search ,function($q) use ($request){
            $q->where('name', 'LIKE', "%{$request->search}%");
        })->when($request->role ,function($q) use ($request){
            $q->whereRelation('roles', 'name', $request->role);
        });
        $count = $permissions->count();
        $skip = ($request->has('skip')) ? $request->skip : 0;
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
