<?php


namespace AdminBase\Extensions\Exporter;


use Encore\Admin\Grid\Exporters\CsvExporter;
use Encore\Admin\Auth\Permission;

/**
 * csv导出
 * Class BaseCsvExporter
 * @package AdminBase\Extensions\Exporter
 */
abstract class BaseCsvExporter extends CsvExporter
{
    /**
     * 数据原样输出字段
     * @var array
     */
    protected $columnUseOriginalValue = [];

    public function export()
    {
        Permission::check($this->getSlug());
        $this->columnCallback();
        parent::export();
    }

    /**
     * 导出权限节点定义 = table + _export
     * @return string
     */
    private function getSlug()
    {
        return $this->grid->model()->getTable() . '_export';
    }

    /**
     * 字段过滤
     */
    abstract protected function columnCallback();
}