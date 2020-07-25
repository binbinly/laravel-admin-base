<?php

namespace AdminBase\Models\Admin;

use AdminBase\Models\AdminBaseModel;

/**
 * 站点模型
 * Class Site
 * @package AdminBase\Models\Admin
 */
class Site extends AdminBaseModel
{
    protected $table = 'sites';

    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(SiteCategory::class);
    }
}
