<?php

namespace AdminBase\Controllers\Auth;

use AdminBase\Controllers\HttpController;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use Illuminate\Http\RedirectResponse;

class SecurityController extends HttpController
{
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
            config('google2fa.otp_input') => 'required',
        ]);

        //retrieve secret
        $secret = $request->session()->pull(config('google2fa.session_var'));

        $authenticator = app(Authenticator::class)->boot($request);

        if ($authenticator->verifyGoogle2FA($secret, (string)$request[config('google2fa.otp_input')])) {
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
            config('google2fa.otp_input') => 'required',
        ]);

        $user = Admin::user();

        $authenticator = app(Authenticator::class)->boot($request);

        if ($authenticator->verifyGoogle2FA($user->google2fa_secret, (string)$request[config('google2fa.otp_input')])) {

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
