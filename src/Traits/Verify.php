<?php


namespace AdminBase\Traits;


use AdminBase\Utility\TnCode;

/**
 * 滑动验证码验证
 * Trait Verify
 * @package AdminBase\Traits
 */
trait Verify
{
    /**
     * 滑动验证码生成
     */
    public function verify(){
        $tn  = new TnCode();
        $tn->make();
    }

    /**
     * 验证
     * @return string
     */
    public function check(){
        if(!session()->has(TnCode::SESSION_OFFSET)) {
            return 'error';
        }
        $offset = intval(request()->input('tn_r', 0));
        if(!$offset) {
            return 'error';
        }
        $r = session()->get(TnCode::SESSION_OFFSET);
        if(abs($r - $offset) <= TnCode::FAULT){
            session()->put(TnCode::SESSION_VERIFY, 1);
            return "ok";
        }
        session()->forget(TnCode::SESSION_OFFSET);
        return 'error';
    }
}