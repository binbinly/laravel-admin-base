<?php

namespace AdminBase\Controllers\WebStack;

use AdminBase\Controllers\AdminBaseController;
use AdminBase\Models\Admin\Site;
use AdminBase\Models\Admin\SiteCategory;
use AdminBase\Traits\FormTrait;
use AdminBase\Traits\GridTrait;
use Encore\Admin\Form;
use Encore\Admin\Grid;

/**
 * 站点导航
 * Class SiteController
 * @package AdminBase\Controllers\WebStack
 */
class SiteController extends AdminBaseController
{
    use GridTrait;
    use FormTrait;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '网站管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Site());

        $grid->id('ID');
        $grid->category()->title('分类');
        $grid->title('标题');
        $grid->thumb('图标')->gallery(['width' => 50, 'height' => 50]);
        $grid->describe('描述')->limit(40);
        $grid->url('地址');

        $grid->disableFilter();
        $grid->disableExport();
        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Site);

        $form->select('category_id', '分类')->options(SiteCategory::selectOptions(null, ''))->rules('required');
        $form->text('title', '标题')->attribute('autocomplete', 'off')->rules('required|max:50');
        $form->image('thumb', '图标')->resize(120, 120)->uniqueName();
        $form->text('describe', '描述')->attribute('autocomplete', 'off')->rules('required|max:300');
        $form->url('url', '地址')->attribute('autocomplete', 'off')->rules('required|max:250');
        $this->disableFormFooter($form);
        return $form;
    }
}