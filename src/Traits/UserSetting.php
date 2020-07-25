<?php


namespace AdminBase\Traits;


use AdminBase\Common\Constant;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Illuminate\Support\Facades\Hash;

/**
 * 用户设置
 * Trait UserSetting
 * @package AdminBase\Traits
 */
trait UserSetting
{
    /**
     * User setting page.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function getSetting(Content $content)
    {
        return $content
            ->title(trans('admin.user_setting'))
            ->body($this->settingForm()->edit(Admin::user()->id))
            ->row(function (Row $row) {
                $row->column(6, function (Column $column) {
                    $column->append($this->settingForm('password')->edit(Admin::user()->id));
                });
                $row->column(6, function (Column $column) {
                    $column->append($this->settingForm('security')->edit(Admin::user()->id));
                });
            });
    }

    /**
     * Model-form for user setting.
     *
     * @param $type
     * @return Form
     */
    protected function settingForm($type = 'basic')
    {
        $class = config('admin.database.users_model');

        $form = new Form(new $class());

        if ($type == 'password') {
            $form->divider('修改密码');
            $form->password('password_current', '当前密码')->required();
            $form->password('password', trans('admin.password'))->rules(Constant::PASSWORD_ROLES, Constant::PASSWORD_ROLES_MSG);
            $form->password('password_confirmation', trans('admin.password_confirmation'))->required()
                ->default(function ($form) {
                    return $form->model()->password;
                });
            $form->ignore(['password_confirmation', 'password_current']);
            $form->setAction(admin_url('auth/setting/password'));

            $form->submitted(function (Form $form) {
                if (!Hash::check(request()->input('password_current', ''), $form->model()->password)) {
                    return back()->withInput()->withErrors([
                        'password_current' => '当前密码不正确',
                    ]);
                }
            });

            $form->saving(function (Form $form) {
                if ($form->password && $form->model()->password != $form->password) {
                    $form->password = bcrypt($form->password);
                }
            });
        } elseif ($type == 'security') {
            $form->divider('安全');
            $user = Admin::user();
            $form->google2fa('google2fa', '二次登陆验证')->secret($user->google2fa_secret);
            if ($user->google2fa_secret){
                $form->setAction(admin_url('auth/setting/disable_2fa'));
            }else{
                $form->setAction(admin_url('auth/setting/enable_2fa'));
            }
            $form->footer(function (Form\Footer $footer) {
                $footer->disableSubmit();
            });
        } else {
            $form->divider('基础信息');
            $form->display('username', trans('admin.username'));
            $form->text('name', trans('admin.name'))->required();
            $form->image('avatar', trans('admin.avatar'))->resize(200, 200);

            $form->setAction(admin_url('auth/setting'));
        }

        $form->saved(function () {
            admin_toastr(trans('admin.update_succeeded'));
            return redirect(admin_url('auth/setting'));
        });

        $form->tools(
            function (Form\Tools $tools) {
                $tools->disableList();
                $tools->disableDelete();
                $tools->disableView();
            }
        );
        $form->footer(function (Form\Footer $footer) {
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
            $footer->disableReset();
        });
        return $form;
    }

    /**
     * 修改密码
     * @return mixed
     */
    public function password()
    {
        return $this->settingForm('password')->update(Admin::user()->id);
    }
}