<?php

namespace AdminBase\Forms\Field;


use AdminBase\Utility\Random;
use Encore\Admin\Form\Field;
use Google2FA;

class Google2FaButton extends Field
{
    protected $view = 'common::google2fa';

    protected $userSecretKey = '';

    public function secret($userSecretKey = ''){
        if ($userSecretKey) {
            $this->userSecretKey = $userSecretKey;
        }
        return $this;
    }

    public function render()
    {
        if ($this->userSecretKey) {
            $valid = 1;
            $this->variables = array_merge($this->variables, compact('valid'));
        }else{
            $secretKey = Google2FA::generateSecretKey(32);
            $inlineUrl = Google2FA::getQRCodeInline(
                config('app.name'),
                config('base.google2fa_email'),
                $secretKey,
                config('base.google2fa_qr_width')
            );
            $valid = 0;
            $recoveryCode = Random::character(32);
            request()->session()->put(config('google2fa.session_var'), $secretKey);
            $this->variables = array_merge($this->variables, compact('secretKey', 'inlineUrl', 'valid', 'recoveryCode'));
        }
        return parent::render();
    }
}
