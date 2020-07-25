<?php

namespace AdminBase\Models;

use AdminBase\Utility\JsonHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Exception;

/**
 * 定义全局的 model 属性
 * Class BaseModel
 * @package App\Model
 */
class AdminBaseModel extends Model
{
    const RELEASE_YES = 1;
    const RELEASE_NO = 0;

    const STATUS_INIT = 0;
    const STATUS_ONLINE = 1;
    const STATUS_OFFLINE = 2;

    public static $statusLabel = [
        self::STATUS_INIT => '待审核',
        self::STATUS_ONLINE => '已通过',
        self::STATUS_OFFLINE => '审核失败',
    ];

    public static $statusLabelForm = [
        self::STATUS_ONLINE => '审核通过',
        self::STATUS_OFFLINE => '审核不通过',
    ];

    public static $releaseLabel = [
        self::RELEASE_NO => '否',
        self::RELEASE_YES => '是'
    ];

    protected $table = '';

    protected $dateFormat = '';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * 时间戳存储
     * @param  $value
     * @return false|int|string
     */
    public function fromDateTime($value)
    {
        return strtotime(parent::fromDateTime($value));
    }
}