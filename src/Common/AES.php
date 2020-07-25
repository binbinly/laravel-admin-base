<?php


namespace AdminBase\Common;

/**
 * AES加密解密
 * Class AES
 * @package AdminBase\Common
 */
class AES
{
    /**
     * 秘钥
     * @var
     */
    private $key;

    /**
     * 加密方式
     * @var string
     */
    private $method = 'AES-128-CBC';

    public function __construct()
    {
        $this->key = config('base.aes_key');
    }

    /**
     * @param string $key
     * @return self
     */
    public function setKey($key): self
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @param string $method
     * @return self
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    /**
     * AES加密算法
     * @param string $content 加密内容
     * @return string
     */
    public function encrypt($content)
    {
        $result = openssl_encrypt($content, $this->method, $this->key, OPENSSL_RAW_DATA, $this->key);
        return base64_encode($result);
    }

    /**
     * AES解密算法
     * @param string $content 密文
     * @return string
     */
    public function decrypt($content)
    {
        $content = base64_decode($content);
        return openssl_decrypt($content, $this->method, $this->key, OPENSSL_RAW_DATA, $this->key);
    }
}
