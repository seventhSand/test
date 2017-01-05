<?php

namespace Webarq\Model;


use Wa;

class AdminModel extends AbstractListingModel
{
    protected $table = 'admins';

    public function getByCredential(array $credentials)
    {
        $select = $this->select('t1.id')->from($this->table . ' as t1');
        foreach ($credentials as $column => $value) {
            $select->where($column, $value);
        }
        return $select->join('admin_roles as t2', 't1.id', 't2.admin_id')
                ->join('roles as t3', 't2.role_id', 't3.id')
                ->where('t1.is_active', 1)
                ->where('t3.is_active', 1)
                ->where('t3.is_admin', 1)
                ->limit(1)
                ->get()
                ->first();
    }

    public function roles()
    {
        return $this->hasMany('\Webarq\Model\AdminRoleModel', Wa::table($this->table)->getReferenceKeyName())
                ->select('roles.id', 'roles.title', 'roles.role_level as level')
                ->join('roles', 'roles.id', 'admin_roles.role_id')
                ->whereIsAdmin(1)
                ->whereIsActive(1);

    }

    public function permissions()
    {
        return $this->hasMany('\Webarq\Model\PermissionModel', 'role_id')
                ->select('permissions.module', 'permissions.panel', 'permissions.permission');
    }
}