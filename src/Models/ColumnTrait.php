<?php


namespace AdminBase\Models;

use AdminBase\Utility\JsonHelper;
use Exception;
use Illuminate\Support\Facades\Cache;

/**
 * map格式缓存数据
 * Trait ColumnTrait
 * @package AdminBase\Models
 */
Trait ColumnTrait
{
    public static function getCacheKey($key = 'List')
    {
        return basename(str_replace('\\', '/', self::class)) . $key;
    }

    /**
     * 获取map数组
     * @param $id
     * @return array|string
     * @throws Exception
     */
    public static function columnAll($id = null)
    {
        if (!Cache::has(self::getCacheKey())) {
            //获取数据
            $list = self::getAll();
            if ($list) {
                Cache::put(self::getCacheKey(), JsonHelper::encode($list), config('base.cache_expire'));
            }
        } else {
            $list = JsonHelper::decode(Cache::get(self::getCacheKey()));
        }
        if (!is_null($id)){
            return $list[$id] ?? '';
        }
        return $list ?: [];
    }
}