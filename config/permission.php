<?php

// config/permission.php
// Key settings for this multi-school system

return [

    'models' => [
        'permission' => Spatie\Permission\Models\Permission::class,
        'role'       => Spatie\Permission\Models\Role::class,
    ],

    'table_names' => [
        'roles'                 => 'roles',
        'permissions'           => 'permissions',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles'       => 'model_has_roles',
        'role_has_permissions'  => 'role_has_permissions',
    ],

    'column_names' => [
        'role_pivot_key'       => null, // default: role_id
        'permission_pivot_key' => null, // default: permission_id
        'model_morph_key'      => 'model_id',
        'team_foreign_key'     => 'team_id', // this is school_id
    ],

    // ✅ TEAMS = true means roles are scoped per school
    // Every role/permission check is automatically filtered by team_id (school_id)
    'teams' => false,

    'register_permission_check_method' => true,
    'register_octane_reset_listener'   => false,

    'cache' => [
        'expiration_time'  => \DateInterval::createFromDateString('24 hours'),
        'key'              => 'spatie.permission.cache',
        'store'            => 'default',
    ],
];