<?php

namespace AdminBase\Common;

/**
 * 格式化
 * Class Format
 * @package AdminBase\Common
 */
class Format
{
    /**
     * 格式化请求参数（针对搜索时间格式类型不同）
     * @param array $params
     * @return array
     */
    public static function formatRequest(array $params)
    {
        if ($params) {
            foreach ($params as &$val) {
                if ($val) {
                    $val = self::formatInt($val);
                }
            }
        }
        return $params;
    }

    /**
     * 格式化成数值
     * @param $value
     * @return array|int
     */
    public static function formatInt($value)
    {
        if (is_numeric($value)) {
            return self::formatNumber($value, false);
        } elseif (is_array($value)) {
            foreach ($value as $k => $v) {
                if (is_numeric($v)) {
                    $value[$k] = self::formatNumber($v, false);
                }
            }
            return $value;
        }
        return $value;
    }

    /**
     * 格式化数值，保留两位小数
     * @param $value
     * @param bool $format 是否格式化为数字字符串
     * @return float
     */
    public static function formatNumber($value, $format = true)
    {
        if ($value == 0) return 0;
        if ($format) {
            return number_format(intval($value));
        } else {
            return intval($value);
        }
    }

    /**
     * 格式化成分
     * @param $value
     * @return int
     */
    public static function formatAmountToPenny($value)
    {
        return intval($value * 100);
    }

    /**
     * 格式化为元
     * @param $value
     * @param bool $format 是否格式化为数字字符串
     * @return float|int
     */
    public static function formatAmountToYuan($value, $format = true)
    {
        if ($value == 0) return 0;
        if ($format) {
            return number_format(intval($value / 100));
        } else {
            return intval($value / 100, 2);
        }
    }

    /**
     * 格式化二维数组成map结构
     * @param $list
     * @param $key
     * @param $value
     * @return array|false
     */
    public static function formatColumn(array $list, $key, $value)
    {
        return array_combine(array_column($list, $key), array_column($list, $value));
    }

    /**
     * 格式化百分比
     * @param $decimal
     * @return string
     */
    public static function formatDecimal($decimal)
    {
        if ($decimal == 0) {
            return '0.00%';
        }
        return sprintf("%.2f", $decimal).'%';
    }

    /**
     * 多维数组排序
     * @param $arr
     * @param $key
     * @param $order
     * @return mixed
     */
    public static function arraySort(array $arr, $key, $order)
    {
        $date = array_column($arr, $key);
        $orderBy = '';
        if ($order == 'DESC') {
            $orderBy = SORT_DESC;
        } elseif ($order == 'ASC') {
            $orderBy = SORT_ASC;
        }
        array_multisort($date, $orderBy, $arr);
        return $arr;
    }
}