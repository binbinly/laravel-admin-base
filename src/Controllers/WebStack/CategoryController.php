<?php

namespace AdminBase\Controllers\WebStack;

use AdminBase\Controllers\AdminBaseController;
use AdminBase\Models\Admin\SiteCategory;
use AdminBase\Traits\FormTrait;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;

/**
 * 站点导航分类
 * Class CategoryController
 * @package AdminBase\Controllers\WebStack
 */
class CategoryController extends AdminBaseController
{
    use FormTrait;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '网站分类';

    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return Admin::content(function (Content $content) {
            $content->header($this->title);
            $content->body(SiteCategory::tree(function ($tree) {
                $tree->nestable(['maxDepth' => 2]);
                $tree->branch(function ($branch) {
                    $icon = '<i class="fa fa-fw ' . $branch['icon'] . '"></i>';
                    return "$icon {$branch['title']} ";
                });
            }));
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new SiteCategory);

        $form->select('parent_id', '父级')->options(SiteCategory::selectOptions())->rules('required');
        $form->text('title', '标题')->rules('required|max:50')->placeholder('不得超过50个字符');
        $form->icon('icon', '图标')->default('fa-star-o')->rules('required|max:20');

        $this->disableFormFooter($form);
        return $form;
    }
}