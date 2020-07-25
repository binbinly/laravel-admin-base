<?php


namespace AdminBase\Traits;

use Encore\Admin\Show;

/**
 * 模型详情
 * Trait ShowTrait
 * @package AdminBase\Traits
 */
trait ShowTrait
{
    /**
     * 显示开始/修改时间
     * @param Show $show
     */
    protected function setShowTimeView(Show &$show)
    {
        $show->field('created_at', trans('admin.created_at'));
        $show->field('updated_at', trans('admin.updated_at'));
    }
}