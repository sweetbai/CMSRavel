<?php

namespace Modules\Common\Controller;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Trait Expend
 * @package Modules\Common\Controller
 * @method \Modules\Common\UI\Table table()
 * @method \Modules\Common\UI\Form form($id = 0):
 * @method storeData($data, $id)
 * @method updateData($data, $id)
 * @method delData($id = 0)
 * @method clearData($id, $info)
 */
trait Expend
{

    public string $model;
    public ?string $indexUrl = null;

    public function index()
    {
        return $this->table()->render();
    }

    public function ajax()
    {
        return $this->table()->renderAjax();
    }

    public function page($id = 0)
    {
        $form = $this->form($id);
        if ($id && $form->modelElo()) {
            $form->setKey($form->modelElo()->getKeyName(), $id);
        }
        return $form->render();
    }

    public function save($id = 0)
    {
        $form = $this->form($id);
        if ($id && $form->modelElo) {
            $form->setKey($form->modelElo()->getKeyName(), $id);
        }
        $data = $form->save();
        if ($data instanceof Collection && method_exists($this, 'storeData')) {
            $this->storeData($data, $id);
        }
        $action = $this->indexUrl !== null ? $this->indexUrl : url(\Str::beforeLast(request()->path(), '/save'));
        return app_success('保存记录成功', [], $action);
    }

    public function del($id = 0)
    {
        if (!$id) {
            $id = request()->input('id');
        }
        if (!$id) {
            app_error('删除参数错误');
        }
        DB::beginTransaction();
        $status = false;
        if (method_exists($this, 'delData')) {
            $status = $this->delData($id);
            if (!$status) {
                DB::rollBack();
                app_error('删除记录失败');
            }
        }
        if ($this->model) {
            $status = $this->model::find($id)->destroy($id);
        }
        if (!$status) {
            DB::rollBack();
            app_error('删除记录失败');
        }
        DB::commit();
        return app_success('删除记录成功');
    }

    public function recovery($id = 0)
    {
        if (!$id) {
            $id = request()->input('id');
        }
        if (!$id) {
            app_error('参数错误');
        }
        if ($this->model) {
            $this->model::withTrashed()->find($id)->restore();
        }
        return app_success('恢复记录成功');
    }

    public function clear($id = 0)
    {
        if (!$id) {
            $id = request()->input('id');
        }
        if (!$id) {
            app_error('参数错误');
        }
        DB::beginTransaction();
        $info = $this->model::withTrashed()->find($id);
        if (method_exists($this, 'clearData')) {
            $status = $this->clearData($id, $info);
            if (!$status) {
                DB::rollBack();
                app_error('删除记录失败');
            }
        }
        if ($this->model) {
            $info->forceDelete();
        }
        DB::commit();
        return app_success('删除记录成功');
    }

    public function status($id = 0)
    {
        if (!$id) {
            $id = request()->input('id');
        }
        if (!$id) {
            app_error('参数错误');
        }
        $value = request()->input('value');
        $field = request()->input('field', 'status');
        if (!$id || !$field) {
            app_error('状态参数传递错误');
        }
        $model = $this->model::find($id);
        $model->{$field} = $value;
        $model->save();
        return app_success('更改状态成功');
    }


    public function data()
    {
        $name = request()->get('query');
        $limit = request()->get('limit', 10);
        $id = request()->get('id');
        $type = request()->get('type');
        $parentId = request()->get('parent');
        $level = request()->get('level');
        $data = new $this->model();
        $key = $data->getKeyName();
        if ($name) {
            $nameKey = [];
            if (method_exists($this, 'dataSearch')) {
                $nameKey = $this->dataSearch();
            }
            foreach ($nameKey as $vo) {
                $data = $data->orWhere($vo, 'like', "%{$name}%");
            }
        }
        if ($id) {
            $ids = !is_array($id) ? explode(',', $id) : $id;
            $ids = array_filter($ids);
            if ($ids) {
                $data = $data->whereIn($key, $ids);
            }
        } elseif ($type == 'linkage' && $level) {
            $data = $data->where($level, $parentId ?: 0);
        }

        if (method_exists($this, 'dataWhere')) {
            $data = $this->dataWhere($data);
        }

        $field = ['name'];
        if (method_exists($this, 'dataField')) {
            $field = $this->dataField();
        }
        $field[] = $key . ' as id';
        $data = $data->paginate($limit, $field);

        $totalPage = $data->lastPage();
        $data = $data->toArray();

        $manageUrl = false;
        if (method_exists($this, 'dataManageUrl')) {
            $manageUrl = true;
        }
        $infoUrl = false;
        if (method_exists($this, 'dataInfoUrl')) {
            $infoUrl = true;
        }
        foreach ($data['data'] as &$item) {
            if ($manageUrl) {
                $item['manage_url'] = $this->dataManageUrl($item);
            }
            if ($infoUrl) {
                $item['info_url'] = $this->dataInfoUrl($item);
            }
        }

        return app_success('ok', [
            'data' => $data['data'],
            'total' => $data['total'],
            'page' => $totalPage
        ]);
    }
}