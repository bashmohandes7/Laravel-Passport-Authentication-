<?php

namespace App\Transformers\Admin\Roles;

use App\Transformers\Admin\Permissions\PermissionTransformer;
use League\Fractal\TransformerAbstract;
use Spatie\Permission\Models\Role;

class RolesTransformer extends TransformerAbstract
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
        'permissions'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Role $role)
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
        ];
    }
    public function includePermissions(Role $role)
    {
        return $this->collection($role->permissions, new PermissionTransformer());
    }
}
