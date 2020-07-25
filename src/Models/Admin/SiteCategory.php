<?php

namespace AdminBase\Models\Admin;

use AdminBase\Models\AdminBaseModel;
use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;

/**
 * 站点分类模型
 * Class SiteCategory
 * @package AdminBase\Models\Admin
 */
class SiteCategory extends AdminBaseModel
{
    use ModelTree, AdminBuilder;

    protected $table = 'site_category';

    public $timestamps = false;

    public function children()
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function sites()
    {
        return $this->hasMany(Site::class, 'category_id');
    }
}
