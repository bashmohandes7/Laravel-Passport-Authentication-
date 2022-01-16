<?php

namespace App\Transformers\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Transformers\Admin\Permissions\PermissionTransformer;
use App\Transformers\Admin\Roles\RolesTransformer;
use League\Fractal\TransformerAbstract;

class AdminTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'roles', 'permissions'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Admin $admin)
    {
             return [
                 'id' => (int)$admin->id,
                 'name' => $admin->name,
                 'email' => $admin->email,
             ];
    }
    public function includeRoles(Admin $admin )
    {
        return $this->collection($admin->roles, new RolesTransformer());
    }
    public function includePermissions(Admin $admin)
    {
        return $this->collection($admin->getAllPermissions(), new PermissionTransformer());
    }
}
