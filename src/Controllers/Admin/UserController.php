<?php


namespace AdminBase\Controllers\Admin;


use AdminBase\Common\Constant;
use AdminBase\Models\Admin\User;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;

class UserController extends \Encore\Admin\Controllers\UserController
{
    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $userModel = config('admin.database.users_model');
        $roleModel = config('admin.database.roles_model');

        $form = new Form(new $userModel());

        $userTable = config('admin.database.users_table');
        $connection = config('admin.database.connection');

        $form->display('id', 'ID');
        $form->text('username', trans('admin.username'))
            ->creationRules(['required', 'min:6', "unique:{$connection}.{$userTable}"])
            ->updateRules(['required', 'min:6', "unique:{$connection}.{$userTable},username,{{id}}"]);

        $form->text('name', trans('admin.name'))->rules('required');
        $form->image('avatar', trans('admin.avatar'))->resize(200, 200);
        $form->password('password', trans('admin.password'))
            ->creationRules(Constant::PASSWORD_ROLES, Constant::PASSWORD_ROLES_MSG)
            ->updateRules(['required', 'min:8', 'confirmed'])->help('密码必须是数字，字母，符号的组合');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });

        $form->ignore(['password_confirmation']);

        $form->multipleSelect('roles', trans('admin.roles'))->options($roleModel::all()->pluck('name', 'id'))->required();

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        if(Admin::user()->isAdministrator()) {//超级管理员才可以操作
            $form->switch('enabled', '是否正常')->states(Constant::STATUS_SWITCH);
            $form->switch('is_validate', '二次登录验证')->states(Constant::TWO_FA_STATUS_SWITCH);
        }

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });

        return $form;
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $userModel = config('admin.database.users_model');

        $grid = new Grid(new $userModel());

        $grid->column('id', 'ID')->sortable();
        $grid->column('username', trans('admin.username'));
        $grid->column('name', trans('admin.name'));
        $grid->column('roles', trans('admin.roles'))->pluck('name')->label();
        $grid->column('created_at', trans('admin.created_at'));
        $grid->column('updated_at', trans('admin.updated_at'));
        if (Admin::user()->isAdministrator()) {
            $grid->column('is_validate', '二次登录验证')->switch(Constant::TWO_FA_STATUS_SWITCH)->help('超级管理员不需要进行二次验证，其他账户开启即可');
        }


        return $grid;
    }
}