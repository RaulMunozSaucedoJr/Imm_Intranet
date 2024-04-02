<?php

namespace OpenAdmin\Admin\Controllers;

use Illuminate\Support\Facades\Hash;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;

class UserController extends AdminController
{
    /**
     * {@inheritdoc}
     */
    protected function title()
    {
        return trans('admin.administrator');
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $userModel = config('admin.database.users_model');

        $grid = new Grid(new $userModel());

        $grid->column('id', 'ID')->sortable();
        $grid->column('username', trans('admin.username'));
        $grid->column('name', trans('admin.name'));
        $grid->column('area', 'Área de Trabajo');
        $grid->column('roles', trans('admin.roles'))->pluck('name')->label();
        $grid->column('created_at', trans('admin.created_at'));
        $grid->column('updated_at', trans('admin.updated_at'));

        $grid->actions(function (Grid\Displayers\Actions\Actions $actions) {
            if ($actions->getKey() == 1) {
                $actions->disableDelete();
            }
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $userModel = config('admin.database.users_model');

        $show = new Show($userModel::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('username', trans('admin.username'));
        $show->field('name', trans('admin.name'));
        $show->field('entryDate', trans('Fecha de Ingreso'));
        $show->field('totalDays', trans('Días Totales de Vacaciones'));
        $show->field('area', trans('Área Laboral'));
        $show->field('boss', trans('Jefe Directo'));
        $show->field('headquarter', trans('Sede Laboral'));
        $show->field('roles', trans('admin.roles'))->as(function ($roles) {
            return $roles->pluck('name');
        })->label();
        $show->field('permissions', trans('admin.permissions'))->as(function ($permission) {
            return $permission->pluck('name');
        })->label();
        $show->field('created_at', trans('admin.created_at'));
        $show->field('updated_at', trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $userModel = config('admin.database.users_model');
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');

        $form = new Form(new $userModel());

        $userTable = config('admin.database.users_table');
        $connection = config('admin.database.connection');

        $form->display('id', 'ID');
        $form->text('username', trans('admin.username'))
            ->creationRules(['required', "unique:{$connection}.{$userTable}"])
            ->updateRules(['required', "unique:{$connection}.{$userTable},username,{{id}}"]);
        $form->text('name', trans('admin.name'))->rules('required');
        $form->email('email', 'Correo Electrónico');
        $form->email('periodOne', 'Periodo 2021-2022')->disabled()->readonly()->icon('');
        $form->email('periodTwo', 'Periodo 2022-2023')->disabled()->readonly()->icon('');
        $form->email('periodThree', 'Periodo 2023-2024')->disabled()->readonly()->icon('');
        $form->text('totalDays', 'Días Totales de Vacaciones')->rules('required')->disabled()->readonly()->icon('');
        $form->text('area', 'Área Laboral')->disabled()->icon('');
        $form->select('area', 'Área Laboral')->options([
            '' => 'Seleccione un área',
            'Administración-Compras-Cajas-Nómina-Asistente-Administrativo' => 'Administración-Compras-Cajas-Nómina-Asistente-Administrativo',
            'Auditoría' => 'Auditoría',
            'Logística' => 'Logística',
            'Atenciónalcliente' => 'Atención al cliente',
            'Mantenimiento-TI' => 'Mantenimiento - TI',
            'Operaciones-Gerencias' => 'Operaciones - Gerencias',
            'Operaciones' => 'Operaciones',
            'Transferencias' => 'Transferencias',
            'RH-Intendencia-Chóferes-Asistente-Administrativo' => 'RH-Intendencia-Chóferes-Asistente y Administrativo',
            'Desarrollo' => 'Desarrollo',
        ]);
        $form->image('avatar', trans('admin.avatar'));
        $form->password('password', trans('admin.password'))->rules('required|confirmed');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });

        $form->ignore(['password_confirmation']);

        $form->multipleSelect('roles', trans('admin.roles'))->options($roleModel::all()->pluck('name', 'id'));
        $form->multipleSelect('permissions', trans('admin.permissions'))->options($permissionModel::all()->pluck('name', 'id'));

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = Hash::make($form->password);
            }
        });

        $form->footer(function ($footer) {
            // disable reset btn
            $footer->disableReset();
            // disable `View` checkbox
            $footer->disableViewCheck();
            // disable `Continue editing` checkbox
            $footer->disableEditingCheck();
            // disable `Continue Creating` checkbox
            $footer->disableCreatingCheck();

        });

        return $form;
    }
    
}
