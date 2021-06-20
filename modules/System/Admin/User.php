<?php

namespace Modules\System\Admin;

use Illuminate\Validation\Rule;
use Modules\Common\UI\Table;

class User extends \Modules\System\Admin\Expend
{

    public string $model = \Modules\System\Model\SystemUser::class;

    public $route = 'admin.system.user';

    public $roleModel = \Modules\System\Model\SystemRole::class;

    use \Modules\System\Common\User {
        \Modules\System\Common\User::table as traitTable;
        \Modules\System\Common\User::Form as traitForm;
    }


    protected function table(): Table
    {
        $table = $this->traitTable();
        $table->filter('角色', 'role_id', function ($query, $value) {
            $query->whereHas('roles', function ($query) use ($value) {
                $query->where((new $this->model)->roles()->getTable() . '.role_id', $value);
            });
        })->select(function () {
            return $this->roleModel::pluck('name', 'role_id')->toArray();
        })->quick();

        $table->column('角色', 'roles.name')->sort(0);
        return $table;
    }

    protected function form(int $id = 0): \Modules\Common\UI\Form
    {
        $form = $this->traitForm($id);
        $form->select('角色', 'role_ids', function () {
            return $this->roleModel::pluck('name', 'role_id');
        }, 'roles')->multi()->verify([
            'required',
        ], [
            'required' => '请选择角色',
        ])->sort(-1);

        return $form;
    }

}