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
    /**
     * 是否查询的 mongodb 数据库
     * @var bool
     */
    protected $isMongodb = false;

    /**
     * 查询的字段是否是时间格式（yyyy-MM-dd H:m:s）
     * @var bool
     */
    protected $isSelectTime = false;

    /**
     * 是否查询当前表子字段
     * @var bool
     */
    protected $isSub = false;

    public function mongodb()
    {
        $this->isMongodb = true;
        return $this;
    }

    public function selectTime()
    {
        $this->isSelectTime = true;
        return $this;
    }

    public function sub(){
        $this->isSub = true;
        return $this;
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
        if ($this->ignore) {
            return;
        }

        if (!Arr::has($inputs, $this->column)) {
            return;
        }

        $this->value = Arr::get($inputs, $this->column);

        $value = array_filter($this->value, function ($val) {
            return $val !== '';
        });

        if (empty($value)) {
            return;
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

    /**
     * 处理mongodb查询子字段
     * @return array|mixed
     */
    protected function buildCondition()
    {
        if ($this->isSub) {
            return [$this->query => func_get_args()];
        }
        return parent::buildCondition();
    }
}
