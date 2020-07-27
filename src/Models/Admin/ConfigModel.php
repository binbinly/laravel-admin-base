<?php


namespace AdminBase\Models\Admin;


use AdminBase\Common\Format;
use AdminBase\Models\AdminBaseModel;
use AdminBase\Utility\JsonHelper;
use Illuminate\Support\Facades\Redis;
use AdminBase\Models\ColumnTrait;
use Exception;

abstract class ConfigModel extends AdminBaseModel
{
    use ColumnTrait;

    const CACHE_EXPIRE = 3600;  //配置缓存时间

    const TYPE_STRING = 1;
    const TYPE_JSON = 2;
    const TYPE_COLUMN = 3;

    /**
     * 配置加载
     * @param null $name
     * @param integer $format
     * @return array|mixed
     * @throws Exception
     */
    public static function init($name = null, $format = self::TYPE_STRING)
    {
        if (!Redis::exists(self::getCacheKey())) {
            $list = self::query()->pluck('value', 'name')->toArray();
            if ($list) {
                Redis::setex(self::getCacheKey(), self::CACHE_EXPIRE, JsonHelper::encode($list));
            }
        } else {
            $list = JsonHelper::decode(Redis::get(self::getCacheKey()));
        }
        if ($name) {
            if ($format == self::TYPE_JSON) {
                return $list[$name] ?? '';
            } elseif ($format == self::TYPE_COLUMN) {
                $data=$list[$name];
                if(!is_array($list[$name])){
                    $data= JsonHelper::decode($list[$name]);
                }
                return Format::formatColumn($data, 'id', 'name');
            }
            return $list[$name] ?? '';
        }
        return $list ?: [];
    }
}