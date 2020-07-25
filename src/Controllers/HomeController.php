<?php

namespace AdminBase\Controllers;

use AdminBase\Common\Constant;
use AdminBase\Models\Admin\SiteCategory;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Database\Query\Builder;

/**
 * 首页
 * Class HomeController
 * @package AdminBase\Controllers
 */
class HomeController extends HttpController
{
    public function index(){
        $categories = SiteCategory::with(['children' => function ($query) {
            $query->orderBy('order');
        }, 'sites'])
            ->withCount('children')
            ->orderBy('order')
            ->get();

        return view('common::webstack.index')->with('categories', $categories)->with('newRecoveryCode', session()->get(Constant::NEW_RECOVERY_CODE));
    }

    public function info(Content $content)
    {
        return $content
            ->title('系统信息')
            ->row(Dashboard::title())
            ->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
    }
}
