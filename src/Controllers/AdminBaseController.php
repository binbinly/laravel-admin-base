<?php

namespace AdminBase\Controllers;

use AdminBase\Common\Format;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\MessageBag;

/**
 * 定义全局的一些东西，比如：开关样式、全局提示语，全局功能禁用、重写
 * Class BaseController
 * @package App\Admin\Controllers
 */
class AdminBaseController extends AdminController
{
    /**
     * 列表自定义参数
     * @var array
     */
    protected $params;

    /**
     * 修改时的主键ID
     * @var int
     */
    protected $id;

    /**
     * 列表
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        $this->params = request()->all();
        /** @var Grid $grid */
        $grid = $this->grid();
        if($grid->option('show_exporter')){//导出功能开启，则权限判断是否有权限
            if(!Admin::user()->can($grid->model()->getTable() . '_export')){//导出权限为数据库表 + '_export'
                $grid->disableExport();
            }
        }
        return $content
            ->title($this->title())
            ->description($this->description['index'] ?? trans('admin.list'))
            ->body($grid);
    }

    /**
     * 编辑
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        $id = intval($id);
        $this->id = $id;
        return parent::edit($id, $content);
    }

    /**
     * json格式成功返回
     * @param string $message
     * @param array $data
     * @param array $ext
     * @return JsonResponse
     */
    protected function success($message = '操作成功', array $data = [], array $ext = [])
    {
        $status = 0;
        return response()->json(compact('status', 'message', 'data', 'ext'));
    }

    /**
     * json格式失败返回
     * @param string $message
     * @return JsonResponse
     */
    protected function error($message = '操作失败')
    {
        return response()->json(['status' => -1, 'message' => $message]);
    }

    /**
     * larave-admin 全局失败提示
     * @param string $msg
     * @return RedirectResponse
     */
    protected function alertError($msg = '操作失败')
    {
        $error = new MessageBag([
            'title' => $msg,
            'message' => $msg,
        ]);
        return back()->with(compact('error'));
    }

    /**
     * 全局成功提示
     * @param string $msg
     * @return RedirectResponse
     */
    protected function alertSuccess($msg = '操作成功')
    {
        admin_success($msg);
        return back();
    }

    /**
     * 兼容mongodb
     * @param $id
     * @return Response
     */
    public function update($id)
    {
        $id = Format::formatInt($id);
        return $this->form()->update($id);
    }
}