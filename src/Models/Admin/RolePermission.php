<?php


namespace AdminBase\Models\Admin;


use AdminBase\Models\AdminBaseModel;
use Encore\Admin\Facades\Admin;

/**
 * 角色权限模型
 * Class RolePermission
 * @package AdminBase\Models\Admin
 */
class RolePermission extends AdminBaseModel
{
    protected $table = 'admin_role_permissions';

    /**
     * 获取当前用户所有路由
     * @return array
     */
    public static function allowRouter(){
        $roles = Admin::user()->roles->toArray();
        $roleIds = array_column($roles, 'id');
        $list = self::query()->leftJoin('admin_permissions as p', 'p.id', '=', 'admin_role_permissions.permission_id')
            ->whereIn('admin_role_permissions.role_id', $roleIds)
            ->where('http_path', '<>', '')->pluck('http_path')->toArray();
        return $list ?: [];
    }

    /**
     * 获取权限id组
     * @param $roleIds
     * @return array
     */
    public static function allowPermission($roleIds){
        $list = self::query()->where('role_id', $roleIds)->pluck('permission_id')->toArray();
        return $list ?: [];
    }
}