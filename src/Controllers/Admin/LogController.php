<?php


namespace AdminBase\Controllers\Admin;

use AdminBase\Actions\Post\LogInput;
use AdminBase\Models\Admin\User;
use AdminBase\Traits\Search;
use Encore\Admin\Auth\Database\OperationLog;
use Encore\Admin\Grid;
use Illuminate\Support\Arr;

/**
 * 操作日志
 * Class LogController
 * @package AdminBase\Controllers\Admin
 */
class LogController extends \Encore\Admin\Controllers\LogController
{
    use Search;

    /**
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new OperationLog());
        $grid->disableExport();

        $grid->model()->orderBy('id', 'DESC');

        $grid->column('id', 'ID')->sortable();
        $grid->column('user.name', '用户名称');
        $grid->column('ip', 'IP地址')->label('primary');
        $grid->column('method')->display(function ($method) {
            $color = Arr::get(OperationLog::$methodColors, $method, 'grey');
            return "<span class=\"badge bg-$color\">$method</span>";
        });
        $grid->column('do', '操作');
        $grid->column('created_at', trans('admin.created_at'));
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableEdit();
            $actions->disableView();
            $actions->disableDelete();
            //模态框 form 表单提交
            $actions->add(new LogInput());
        });

        $grid->disableCreateButton();

        return $grid;
    }

    /**
     * @inheritDoc
     */
    protected function filter(Grid\Filter &$filter)
    {
        $filter->equal('user_id', '用户')->select(User::all()->pluck('name', 'id'));
        $filter->equal('method')->select(array_combine(OperationLog::$methods, OperationLog::$methods));
        $filter->equal('ip');
    }
}