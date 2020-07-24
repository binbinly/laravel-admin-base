<?php

namespace AdminBase\Controllers\Auth;

use AdminBase\Controllers\HttpController;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use Illuminate\Http\RedirectResponse;

class SecurityController extends HttpController
{
    const INPUT_KEY = 'one_time_password';

    /**
     * 开启二次验证
     * @param Request $request
     * @return RedirectResponse
     */
    public function validateTwoFactor(Request $request)
    {
        $user = Admin::user();
        if ($user->google2fa_secret) {
            return $this->error('重复操作');
        }

        $this->validate($request, [
            self::INPUT_KEY => 'required',
        ]);

        //retrieve secret
        $secret = $request->session()->pull(config('google2fa.session_var'));

        $authenticator = app(Authenticator::class)->boot($request);

        if ($authenticator->verifyGoogle2FA($secret, (string)$request[self::INPUT_KEY])) {
            //encrypt and then save secret
            $user->google2fa_secret = $secret;
            $user->recovery_code = $request['recovery_code'];
            $user->save();

            $authenticator->login();

            admin_success('操作成功');
            return redirect('/');
        }
        return $this->error();
    }

    /**
     * 关闭二次验证
     * @param Request $request
     * @return RedirectResponse
     */
    public function deactivateTwoFactor(Request $request)
    {
        $this->validate($request, [
            self::INPUT_KEY => 'required',
        ]);

        $user = Admin::user();

        //retrieve secret
        $secret = $user->google2fa_secret;

        $authenticator = app(Authenticator::class)->boot($request);

        if ($authenticator->verifyGoogle2FA($secret, (string) $request[self::INPUT_KEY])) {

            //make secret column blank
            $user->google2fa_secret = '';
            $user->recovery_code = '';
            $user->save();

            $authenticator->logout();

            admin_success('操作成功');
            return redirect('/');
        }
        return $this->error();
    }
}
