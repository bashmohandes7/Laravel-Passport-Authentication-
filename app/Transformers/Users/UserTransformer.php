<?php

namespace App\Transformers\Users;

use App\Models\User;
use App\Transformers\Roles\RolesTransformer;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
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
    public function transform(User $user )
    {
        return [
            'id' =>     $user->id,
            'email' =>  $user->email,
            'name' =>   $user->name,
            'created_at'=> Carbon::parse($user->created_at)->format('Y-m-d')
        ];
    }
    public function includeRoles(User $user)
    {
        return $this->collection($user->roles, new RolesTransformer());
    }
}
