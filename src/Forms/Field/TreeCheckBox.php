<?php


namespace AdminBase\Forms\Field;


use AdminBase\Utility\JsonHelper;
use Encore\Admin\Form\Field;

/**
 * 权限树结构
 * Class TreeCheckBox
 * @package AdminBase\Forms\Field
 */
class TreeCheckBox  extends Field
{
    protected $view = 'common::tree-checkbox';

    protected static $css = [
        '/layui/css/layui.css'
    ];

    protected $treeData;

    protected static $js = [
        '/layui/layui.js'
    ];

    public function options($options = [])
    {
        $this->treeData = JsonHelper::encode($options);
    }

    public function render()
    {
        $this->script = <<<EOT
    var data = eval('{$this->treeData}');
  layui.use('tree', function(){
    var tree = layui.tree;
   
    //渲染
    tree.render({
      elem: '#tree-checkbox'
      ,showCheckbox: true
      ,data: data
      ,id: 'permission'
      ,field: 'id'
    });
   
  });
EOT;
        return parent::render();
    }
}