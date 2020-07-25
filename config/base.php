<?php

return [
    //二次登陆验证
    'google2fa_email' => env('GOOGLE_2FA_EMAIL', 'google2fa@pragmarx.com'),
    'google2fa_qr_width' => 200,
    'google2fa_force' => env('GOOGLE_2FA_FORCE', false),    //是否强制开启

    //缓存时间 默认五分钟
    'cache_expire' => env('CACHE_EXPIRE', 5),

    //aes秘钥
    'aes_key' => env('AES_KEY', ''),

    //jwt秘钥
    'jwt_key' => env('JWT_KEY', ''),
];