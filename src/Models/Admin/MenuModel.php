<?php


namespace AdminBase\Models\Admin;

use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Facades\Admin;

/**
 * 菜单模型
 * Class MenuModel
 * @package AdminBase\Models\Admin
 */
class MenuModel extends Menu
{
    /**
     * 解决菜单对应权限显示/隐藏
     * @return array
     */
    public function toTree()
    {
        $menu = $this->buildNestedArray();
        if (Admin::user()->isAdministrator()) {
            return $menu;
        }
        $routers = RolePermission::allowRouter();

        $routers = $this->formatRouters($routers);
        $this->filterMenu($menu, str_replace('/', '', str_replace('*', '', $routers)));
        return $this->filterEmptyMenu($menu);
    }

    /**
     * 过滤没有权限的菜单
     * @param $menu
     * @param $allowRouter
     */
    protected function filterMenu(&$menu, $allowRouter)
    {
        foreach ($menu as $key => &$val) {
            if (isset($val['children']) && $val['children']) {
                $this->filterMenu($val['children'], $allowRouter);
            }
            if ($val['uri'] == '/' || $val['uri'] == '') continue;
            if ($val['parent_id'] != 0 && !in_array(str_replace('/', '', $val['uri']), $allowRouter)) {
                unset($menu[$key]);
            }
        }
    }

    /**
     * 去除没有子菜单的item
     * @param $menu
     * @return array
     */
    protected function filterEmptyMenu($menu)
    {
        return array_filter($menu, function($val){
            if ($val['uri'] == '/') return true;
            if (!isset($val['children']) || !$val['children']) {
                return false;
            }
            return true;
        });
    }

    /**
     * @param $routers
     * @return array
     */
    protected function formatRouters($routers)
    {
        $newRouters = [];
        //权限菜单 $allowRouter 可以是多个的
        array_map(function ($val) use (&$newRouters) {
            if (strpos($val, "\r\n")) {
                $val = explode("\r\n", $val);
                $newRouters = array_merge($newRouters, $val);
            } else {
                array_push($newRouters, $val);
            }
        }, $routers);
        return $newRouters;
    }
}