<?php

namespace AdminBase\Forms\Field;

use Illuminate\Support\Arr;

/**
 * 查询用户列表时间筛选不生效，修改\Encore\Admin\Grid\Filter\Between，适用于mongodb的时间筛选
 * Class Between
 * @package App\Admin\Extensions\Field
 */
class Between extends \Encore\Admin\Grid\Filter\Between
{
    protected $isMongodb = false;

    protected $isSelectTime = false;

    //是否查询的 mongodb 数据库
    public function mongodb()
    {
        $this->isMongodb = true;
    }

    //查询的字段是否是时间格式（yyyy-MM-dd H:m:s）
    public function selectTime()
    {
        $this->isSelectTime = true;
    }

    /**
     * Get condition of this filter.
     *
     * @param array $inputs
     *
     * @return mixed
     */
    public function condition($inputs)
    {
        if (!Arr::has($inputs, $this->column)) {
            return false;
        }

        $this->value = Arr::get($inputs, $this->column);

        $value = array_filter($this->value, function ($val) {
            return $val !== '';
        });

        if (empty($value)) {
            return false;
        }

        if (!isset($value['start']) && isset($value['end'])) {
            if (!$this->isSelectTime) $value['end'] = strtotime($value['end']);
            return $this->buildCondition($this->column, '<=', $value['end']);
        }

        if (!isset($value['end']) && isset($value['start'])) {
            if (!$this->isSelectTime) $value['start'] = strtotime($value['start']);
            return $this->buildCondition($this->column, '>=', $value['start']);
        }

        $this->query = 'whereBetween';

        if (!$this->isSelectTime) {
            $value['start'] = strtotime($value['start']);
            $value['end'] = strtotime($value['end']);
        }

        if ($this->isMongodb) {
            $value = array_values($value);
        }
        return $this->buildCondition($this->column, $value);
    }
}
