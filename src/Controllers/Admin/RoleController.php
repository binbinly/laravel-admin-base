<?php

namespace AdminBase\Controllers\Admin;

use AdminBase\Common\Constant;
use AdminBase\Models\Admin\Permission;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Response;

/**
 * 角色
 * Class RoleController
 * @package AdminBase\Controllers\Admin
 */
class RoleController extends \Encore\Admin\Controllers\RoleController
{
    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->title($this->title())
            ->description($this->description['edit'] ?? trans('admin.edit'))
            ->body($this->form($id)->edit($id));
    }

    /**
     * Make a form builder.
     *
     * @param $id
     * @return Form
     */
    public function form($id = 0)
    {
        $roleModel = config('admin.database.roles_model');

        $form = new Form(new $roleModel());

        if ($id == 1){
            $form->text('slug', trans('admin.slug'))->required();
            $form->text('name', trans('admin.name'))->required();
        } else {
            $form->tab('信息', function (Form $form) {
                $form->text('slug', trans('admin.slug'))->required();
                $form->text('name', trans('admin.name'))->required();
                $form->switch('force_2fa', '强制二次登录验证')->states(Constant::SWITCH);
            })->tab('授权', function ($form) use ($id) {
                $form->tree('permissions', trans('admin.permissions'))->options((new Permission())->layTree($id));
            });
        }
        return $form;
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $roleModel = config('admin.database.roles_model');

        $grid = new Grid(new $roleModel());

        $grid->column('id', 'ID')->sortable();
        $grid->column('slug', trans('admin.slug'));
        $grid->column('name', trans('admin.name'));
        $grid->column('force_2fa')->switch(Constant::SWITCH)->editable();

        $grid->column('created_at', trans('admin.created_at'));
        $grid->column('updated_at', trans('admin.updated_at'));

        return $grid;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function store()
    {   $this->requestFormat();
        return $this->form()->store();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function update($id)
    {
        $this->requestFormat();
        return $this->form()->update($id);
    }

    /**
     * 格式化匹配laravel-admin权限节点
     */
    protected function requestFormat(){
        $data = request()->all();
        foreach($data as $key => $item) {
            if(strpos($key, 'layuiTreeCheck') !== false) {
                $data['permissions'][] = $item;
            }
        }
        request()->merge($data);
    }
}
