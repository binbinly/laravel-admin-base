<?php


namespace AdminBase\Common;


/**
 * 时间
 * Class Date
 * @package AdminBase\Common
 */
class Date
{
    /**
     * 上个月第一天
     * @param string $format
     * @return false|string
     */
    public static function lastMonthStart($format = 'Y-m-d'){
        return date($format, strtotime(date('Y-m-01') . ' -1 month'));
    }

    /**
     * 当月第一天
     * @return false|string
     */
    public static function currMonthStart() {
        return date('Y-m-01');
    }

    /**
     * 上个月最后一天
     * @param string $format
     * @return false|string
     */
    public static function lastMonthEnd($format = 'Y-m-d') {
        return date($format, strtotime(date('Y-m-01') . ' -1 day'));
    }

    /**
     * 昨天
     * @param string $format
     * @return false|string
     */
    public static function yesterday($format = 'Y-m-d'){
        return date($format, strtotime('-1 day'));
    }

    /**
     * 当日时间
     * @param string $format
     * @return false|string
     */
    public static function today($format = 'Y-m-d'){
        return date($format);
    }

    /**
     * 当日开始时间
     * @return false|string
     */
    public static function todayStart(){
        return date('Y-m-d 00:00:00');
    }

    /**
     * 时间格式化
     * @param $timestamp
     * @param string $format
     * @return false|string
     */
    public static function format($timestamp, $format = 'Y-m-d H:i:s') {
        if(!is_numeric($timestamp) || $timestamp == 0) return '';
        return date($format, $timestamp);
    }
}