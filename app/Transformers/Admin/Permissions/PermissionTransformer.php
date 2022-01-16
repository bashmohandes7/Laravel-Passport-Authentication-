<?php

namespace App\Transformers\Admin\Permissions;

use App\Transformers\Admin\Roles\RolesTransformer;
use League\Fractal\TransformerAbstract;
use Spatie\Permission\Models\Permission;

class PermissionTransformer extends TransformerAbstract
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
        'roles'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Permission $permission)
    {
        return [
            'id' => $permission->id,
            'name' => $permission->name
        ];
    }
    public function includeRoles(Permission $permission)
    {
        return $this->collection($permission->roles, new RolesTransformer());
    }
}
