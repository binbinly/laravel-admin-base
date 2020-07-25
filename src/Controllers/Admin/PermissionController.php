<?php


namespace AdminBase\Controllers\Admin;


use AdminBase\Models\Admin\Permission;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

/**
 * 权限
 * Class PermissionController
 * @package AdminBase\Controllers\Admin
 */
class PermissionController extends Controller
{
    use HasResourceActions;

    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return trans('admin.permissions');
    }

    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->title($this->title())
            ->description(trans('admin.list'))
            ->row(function (Row $row) {

                $row->column(6, function (Column $column) {
                    $form = new \Encore\Admin\Widgets\Form();
                    $form->action(admin_url('auth/permissions'));

                    $this->initForm($form);

                    $form->hidden('_token')->default(csrf_token());

                    $column->append((new Box(trans('admin.new'), $form))->style('success'));
                });

                $row->column(6, $this->treeView()->render());
            });
    }

    /**
     * Redirect to edit page.
     *
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function show($id)
    {
        return redirect()->route('admin.auth.menu.edit', ['id' => $id]);
    }

    /**
     * Edit interface.
     *
     * @param string  $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->title($this->title())
            ->description(trans('admin.edit'))
            ->row($this->form()->edit($id));
    }

    public function form()
    {
        $form = new Form(new Permission());

        $this->initForm($form);

        return $form;
    }

    /**
     * @return Tree
     */
    protected function treeView()
    {
        return Permission::tree(function (Tree $tree) {
            $tree->disableCreate();

            $tree->branch(function ($branch) {
                $payload = "<i class='fa fa-align-justify'></i>&nbsp;<strong>{$branch['name']}</strong>";

                if (!isset($branch['children'])) {

                    $payload .= "&nbsp;&nbsp;&nbsp;".$branch['slug']."<i class='fa'></i>&nbsp;<strong style='color: red'>{$branch['http_path']}</strong>";
                }

                return $payload;
            });
            $tree->query(function ($model) {
                return $model->where('id', '<>', 1);
            });

        });
    }

    protected function initForm(&$form){
        $form->text('slug', trans('admin.slug'))->rules('required');
        $form->text('name', trans('admin.name'))->rules('required');

        $form->multipleSelect('http_method', trans('admin.http.method'))
            ->options($this->getHttpMethodsOptions())
            ->help(trans('admin.all_methods_if_empty'));
        $form->select('parent_id', '父ID')->options(Permission::selectOptions(null, '所属权限'));
        $form->textarea('http_path', trans('admin.http.path'));
    }

    /**
     * Get options of HTTP methods select field.
     *
     * @return array
     */
    protected function getHttpMethodsOptions()
    {
        $model = config('admin.database.permissions_model');

        return array_combine($model::$httpMethods, $model::$httpMethods);
    }

}