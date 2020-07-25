<?php


namespace AdminBase\Models\Admin;


use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;

/**
 * 权限模型
 * Class Permission
 * @package AdminBase\Models\Admin
 */
class Permission extends \Encore\Admin\Auth\Database\Permission
{
    use AdminBuilder, ModelTree;

    /**
     * @var array
     */
    protected $fillable = ['name', 'slug', 'parent_id', 'order', 'http_method', 'http_path'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTitleColumn('name');
    }

    /**
     * Format data to tree like array.
     *
     * @param $roleId
     * @return array
     */
    public function layTree($roleId)
    {
        $tree = $this->toTree();
        $permission = RolePermission::allowPermission($roleId);
        $fTree = $this->format($tree, $permission);
        $this->formatTree($fTree);
        return $fTree;
    }

    /**
     * 格式化已选择
     * @param $tree
     * @param $permission
     * @return array
     */
    protected function format($tree, $permission){
        $data = [];
        foreach($tree as $key => $item) {
            if ($item['id'] == 1) {//全部权限不可以授予
                continue;
            }
            $data[$key] = [
                'title' => $item['name'],
                'id' => $item['id'],
                'checked' => in_array($item['id'], $permission)
            ];
            if(isset($item['children']) && $item['children']) {
                $data[$key]['children'] = $this->format($item['children'], $permission);
            }
        }
        return array_values($data);
    }

    /**
     * layui树特性只保留最后一级checked
     * @param $tree
     * @return mixed
     */
    protected function formatTree(&$tree) {
        foreach($tree as &$item) {
            if(isset($item['children']) && $item['children']) {

                foreach ($item['children'] as $v) {

                    if ($v['checked']) {
                        $item['checked'] = false;
                        $this->formatTree($item['children']);
                        break;
                    }
                }
            }
        }
        return $tree;
    }
}