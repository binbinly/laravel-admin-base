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
}