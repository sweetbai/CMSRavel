<?php

namespace Modules\System\Admin;

use Illuminate\Validation\Rule;

class FormData extends \Modules\System\Admin\Expend
{

    public string $model = \Modules\System\Model\FormData::class;
    protected $formInfo;

    public function __construct()
    {
        $formId = request()->get('form');
        $this->formInfo = \Modules\System\Model\Form::find($formId);
    }

    protected function table()
    {
        $table = new \Modules\Common\UI\Table(new $this->model());
        $table->model()->orderBy('data_id', 'desc');
        $table->title($this->formInfo->name);

        $table->action()->button('添加', 'admin.system.formData.page', ['form' => $this->formInfo->form_id])->type('dialog');
        $table->filterParams('form', $this->formInfo->form_id);

        foreach ($this->formInfo->data as $vo) {
            if ($vo['type'] == 'text') {
                $table->filter($vo['name'], $vo['field'], function ($query, $value) use ($vo) {
                    $query->where('data->'.$vo['field'], $value);
                })->text('请输入'.$vo['name'].'搜索')->quick();
                break;
            }
        }

        $table->column('#', 'data_id')->width(80);
        foreach ($this->formInfo->data as $vo) {
            if ($vo['list']) {
                if ($vo['type'] == 'image') {
                    $table->column($vo['name'])->image('data->'.$vo['field'], function ($value) {
                        return $value ?: '无';
                    });
                } else {
                    $table->column($vo['name'], 'data->'.$vo['field']);
                }
            }
        }

        $column = $table->column('操作')->width(100);
        $column->link('编辑', 'admin.system.formData.page', ['form' => $this->formInfo->form_id, 'id' => 'data_id'])->type('dialog');
        $column->link('删除', 'admin.system.formData.del', ['form' => $this->formInfo->form_id, 'id' => 'data_id'])->type('ajax')->data(['type' => 'post']);

        return $table;
    }

    public function form(int $id = 0)
    {
        $form = new \Modules\Common\UI\Form();
        $form->dialog(true);
        $form->action(route('admin.system.formData.save', ['id' => $id, 'form' => $this->formInfo->form_id]));
        app(\Modules\System\Service\Form::class)->getFormUI($this->formInfo->form_id, $form, $id);
        return $form;
    }

    public function save($id)
    {
        $data = $this->form($id)->save();
        app(\Modules\System\Service\Form::class)->saveForm($this->formInfo->form_id, $data, $id);
        return app_success('更新'.$this->formInfo['menu'].'成功', [], route('admin.system.formData', ['form' => $this->formInfo->form_id]));
    }

}