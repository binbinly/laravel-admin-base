<?php

namespace AdminBase\Controllers\Auth;

use AdminBase\Common\Constant;
use AdminBase\Controllers\HttpController;
use AdminBase\Utility\Random;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Google2FA;

/**
 * 恢复代码登陆
 * Class RecoveryLoginController
 * @package AdminBase\Controllers\Auth
 */
class RecoveryLoginController extends HttpController
{
    use RedirectsUsers;

    const INPUT_KEY = 'recovery_code';

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function get()
    {
        return view('common::recovery.login');
    }

    /**
     * 提交
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            self::INPUT_KEY => 'required',
        ])->validate();

        $user = Admin::user();
        $recovery = $request->input(self::INPUT_KEY);

        if ($user->recovery_code == $recovery) {
            app('pragmarx.google2fa')->setStateless(false);
            Google2FA::login();

            //更新恢复代码
            $newCode = Random::character(32);
            $user->recovery_code = $newCode;
            $user->save();
            $request->session()->put(Constant::NEW_RECOVERY_CODE, $newCode);
            return redirect($this->redirectPath());
        } else {
            return back()->withInput()->withErrors([self::INPUT_KEY => '恢复代码不匹配']);
        }
    }
}