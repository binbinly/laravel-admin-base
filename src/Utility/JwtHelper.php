<?php


namespace AdminBase\Utility;

use AdminBase\Traits\Singleton;
use \Firebase\JWT\JWT;

class JwtHelper
{
    use Singleton;

    private $key;

    private $sign = 'HS256';

    private $expire = 7200;

    /**
     * JwtHelper constructor.
     * @param string $sign
     * @param int $expire   过期时间
     */
    private function __construct($sign = '', $expire = 0)
    {
        $this->key = config('base.jwt_key');
        $sign && $this->sign = $sign;
        $expire && $this->expire = $expire;
    }

    public function encode(array $data){
        $now = time();
        $payload = array(
            "iat" => $now,    //发布时间
            "exp" => $now + $this->expire //到期时间
        );
        $payload = array_merge($payload, $data);

        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        return JWT::encode($payload, $this->key);
    }

    public function decode($token){
        return (array) JWT::decode($token, $this->key, array($this->sign));
    }
}