<?php


namespace AdminBase\Traits;

use AdminBase\Common\Constant;

/**
 * 分页
 * Trait Page
 * @package AdminBase\Traits
 */
trait Page
{
    /**
     * 参数
     * @var array
     */
    protected $params;

    /**
     * 第几页
     * @var int
     */
    protected $p = 1;

    /**
     * 起始页
     * @var int
     */
    protected $limit;

    /**
     * 格式化分页
     */
    protected function format()
    {
        if (isset($this->params['p'])) {
            $this->p = intval($this->params['p']);
            $this->p < 1 && $this->p = 1;
            $this->limit = ($this->p - 1) * Constant::PAGE_SIZE;
        }
    }
}