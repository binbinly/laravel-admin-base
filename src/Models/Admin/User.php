<?php


namespace AdminBase\Models\Admin;


use Encore\Admin\Auth\Database\Administrator;

/**
 * 用户模型
 * Class UserModel
 * @property $id
 * @property $username
 * @property $name
 * @property $avatar
 * @property $google2fa_secret
 * @property $enabled
 * @property $recovery_code
 * @package App\Model\Admin
 */
class User extends Administrator
{

    const IS_CALIDATE_OFF = 0;

    const IS_CALIDATE_ON = 1;

    public $is_validate_status = [
        self::IS_CALIDATE_OFF => '关闭',
        self::IS_CALIDATE_ON => '开启'
    ];

    protected $fillable = ['username', 'password', 'name', 'avatar', 'google2fa_secret', 'recovery_code'];

    /**
     * Ecrypt the user's google_2fa secret.
     *
     * @param string  $value
     * @return void
     */
    public function setGoogle2faSecretAttribute($value): void
    {
        $this->attributes['google2fa_secret'] = encrypt($value);
    }

    /**
     * Decrypt the user's google_2fa secret.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getGoogle2faSecretAttribute($value): ?string
    {
        return $value ? decrypt($value) : '' ;
    }

    /**
     * Ecrypt the user's recovery_code secret.
     *
     * @param string  $value
     * @return void
     */
    public function setRecoveryCodeAttribute($value): void
    {
        $this->attributes['recovery_code'] = encrypt($value);
    }

    /**
     * Decrypt the user's recovery_code secret.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getRecoveryCodeAttribute($value): ?string
    {
        return $value ? decrypt($value) : '';
    }

    /**
     * 关闭二次验证
     * @param $id
     */
    public static function blank2faToken($id)
    {
        // 字段：google2fa_secret和recovery_code 置为空，is_validate 置为 0，
        self::query()->where('id', $id)->update(['google2fa_secret' => '', 'recovery_code' => '', 'is_validate' => self::IS_CALIDATE_OFF]);
    }
}