<?php

namespace AdminBase\Common {
    interface Constant
    {
        //密码验证规则
        const PASSWORD_ROLES = ['required', 'confirmed', 'min:8', 'max:20', 'regex:/^(\w*(?=\w*\d)(?=\w*[A-Za-z])\w*){8,18}$/'];
        const PASSWORD_ROLES_MSG = ['confirmed' => '两次密码不一样', 'min' => '密码长度8-20', 'max' => '密码过长', 'regex' => '必须包含字母'];

        //二次登陆 - 恢复代码标识key
        const NEW_RECOVERY_CODE = 'new_recovery_code';

        //分页大小
        const PAGE_SIZE = 20;

        //普通开关样式
        const SWITCH = [
            'on' => ['value' => 1, 'text' => '开', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '关', 'color' => 'danger'],
        ];

        //状态开关样式
        const STATUS_SWITCH = [
            'on' => ['value' => 1, 'text' => '启用', 'color' => 'success'],
            'off' => ['value' => 2, 'text' => '冻结', 'color' => 'danger']
        ];

        //二次登陆验证开关样式
        const TWO_FA_STATUS_SWITCH = [
            'on' => ['value' => 1, 'text' => '开启', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger']
        ];

        //状态bool定义
        const STATUS_BOOL = [1 => true, 2 => false];

        //默认label样式
        const LABEL = [0 => 'primary', 1 => 'success', 2 => 'danger'];
    }
}