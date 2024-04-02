<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Post\BatchRestore;
use App\Admin\Actions\Post\Restore;
use App\Admin\Actions\Replicate;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\EmpleadoRH;

class EmpleadoRHController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Colaboradores';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */

    protected function grid()
    {
        $grid = new Grid(new EmpleadoRH());
        $grid->sortable();
        $grid->enableHotKeys();
        $grid->fixColumns(3, -1);
        /*
        $grid->tools(function (Grid\Tools $tools) {
        $tools->append(new \App\Admin\Actions\Page\ImportPage());
        });
         */
        $grid->quickSearch(['NumeroEmpleado', 'Empleado', 'Curp', 'Sede']);
        $grid->actions(function ($actions) {
            $actions->add(new Replicate());
        });

        $grid->filter(function ($filter) {
            $filter->scope('trashed', 'Papelería de Reciclaje')->onlyTrashed();
        });
        $grid->actions(function ($actions) {
            if (request('_scope_') == 'trashed') {
                $actions->add(new Restore());
            }
        });
        $grid->batchActions(function ($batch) {
            if (request('_scope_') == 'trashed') {
                $batch->add(new BatchRestore());
            }
        });
        $grid->selector(function (Grid\Tools\Selector $selector) {
            $selector->select('Sede', 'Sede', [
                'AICM' => 'AICM',
                'AIFA' => 'AIFA',
                'AICM/AIFA' => 'AICM/AIFA',
            ]);
        });
        $grid->actions(function ($actions) {
            $actions->add(new Replicate());
        });

        $grid->column('id', __('Id'));
        $grid->column('NumeroEmpleado', __('Numero de Empleado'))->filter('like');
        $grid->column('Departamento', __('Departamento'))->filter('like');
        $grid->column('Posicion', __('Posicion'))->filter('like');
        $grid->column('Empleado', __('Empleado'))->filter('like');
        $grid->column('FechaEntrada', __('Fecha de Entrada'))->filter('like');
        $grid->column('Direccion', __('Direccion'))->filter('like');
        $grid->column('RFC', __('RFC'))->filter('like');
        $grid->column('Curp', __('Curp'))->filter('like');
        $grid->column('NSS', __('NSS'))->filter('like');
        $grid->column('Banco', __('Banco'))->filter('like');
        $grid->column('CuentaBancaria', __('Cuenta Bancaria'))->filter('like');
        $grid->column('ClabeBanco', __('Clabe Banco'))->filter('like');
        $grid->column('Correo', __('Correo'))->filter('like');
        $grid->column('FechaNacimiento', __('Fecha de Nacimiento'))->filter('like');
        $grid->column('Sede', __('Sede'))->filter('like');
        $grid->column('Estatus', __('Estatus'))->filter('like');
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('deleted_at', __('Deleted at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(EmpleadoRH::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('NumeroEmpleado', __('NumeroEmpleado'));
        $show->field('Departamento', __('Departamento'));
        $show->field('Posicion', __('Posicion'));
        $show->field('Empleado', __('Empleado'));
        $show->field('FechaEntrada', __('FechaEntrada'));
        $show->field('Direccion', __('Direccion'));
        $show->field('RFC', __('RFC'));
        $show->field('Curp', __('Curp'));
        $show->field('NSS', __('NSS'));
        $show->field('Banco', __('Banco'));
        $show->field('CuentaBancaria', __('CuentaBancaria'));
        $show->field('ClabeBanco', __('ClabeBanco'));
        $show->field('Correo', __('Correo'));
        $show->field('FechaNacimiento', __('FechaNacimiento'));
        $show->field('Sede', __('Sede'));
        $show->field('Estatus', __('Estatus'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('deleted_at', __('Deleted at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new EmpleadoRH());

        $form->tab('Registro Individual', function ($form) {
            $form->text('NumeroEmpleado', __('Número de Empleado'))->placeholder('No. Empleado  ')->rules('required|regex:/^[0-9]/|min:3', [
                'regex' => 'Este campo ÚNICAMENTE acepta números/dígitos.',
                'min' => 'Mínimo debe tener 3 carácteres',
            ]);
            $form->text('Departamento', __('Departamento'))->placeholder('Departamento')->rules('required|regex:/^[A-z]/|min:8', [
                'regex' => 'Este campo no acepta caracteres especiales como punto, coma, punto y coma, etc.',
                'min' => 'Mínimo debe tener 8 carácteres',
            ]);
            $form->text('Posicion', __('Posicion'))->placeholder('Posicion')->rules('required|regex:/^[A-z]/|min:8', [
                'regex' => 'Este campo no acepta caracteres especiales como punto, coma, punto y coma, etc.',
                'min' => 'Mínimo debe tener 8 carácteres',
            ]);
            $form->text('Nombre(s)', __('Nombre(s)'))->placeholder('Nombre(s)')->rules('required|regex:/^[A-z]/|min:8', [
                'regex' => 'Este campo no acepta caracteres especiales como punto, coma, punto y coma, etc.',
                'min' => 'Mínimo debe tener 8 carácteres',
            ]);
            $form->text('ApellidoPaterno', __('Apellido Paterno'))->placeholder('Apellido Paterno')->rules('required|regex:/^[A-z]/|min:8', [
                'regex' => 'Este campo no acepta caracteres especiales como punto, coma, punto y coma, etc.',
                'min' => 'Mínimo debe tener 8 carácteres',
            ]);
            $form->text('ApellidoMaterno', __('Apellido Materno'))->placeholder('Apellido Materno')->rules('required|regex:/^[A-z]/|min:8', [
                'regex' => 'Este campo no acepta caracteres especiales como punto, coma, punto y coma, etc.',
                'min' => 'Mínimo debe tener 8 carácteres',
            ]);
            $form->date('FechaEntrada', __('Fecha de Entrada'))->default(date('Y-m-d'))->placeholder('Fecha de Entrada')->required();
            $form->text('Direccion', __('Direccion'))->placeholder('Direccion')->rules('required|regex:/^[A-z]/|min:8', [
                'regex' => 'Este campo no acepta caracteres especiales como punto, coma, punto y coma, etc.',
                'min' => 'Mínimo debe tener 8 carácteres',
            ]);
            $form->text('RFC', __('RFC'))->placeholder('RFC')->rules('required|regex:/^[A-z]/|min:8', [
                'regex' => 'Este campo no acepta caracteres especiales como punto, coma, punto y coma, etc.',
                'min' => 'Mínimo debe tener 8 carácteres',
            ]);
            $form->text('Curp', __('Curp'))->placeholder('Curp')->rules('required|regex:/^[A-z]/|min:8', [
                'regex' => 'Este campo no acepta caracteres especiales como punto, coma, punto y coma, etc.',
                'min' => 'Mínimo debe tener 8 carácteres',
            ]);
            $form->text('NSS', __('NSS'))->placeholder('NSS')->rules('required|regex:/^(\d{2})(\d{2})(\d{2})\d{5}$/|min:4', [
                'regex' => 'Este campo no acepta espacios en blanco.',
                'min' => 'Mínimo debe tener 4 carácteres',
            ])->help('Si al momento de recuperar la información de este campo tiene espacios en blanco eliminelos.');
            $form->text('Banco', __('Banco'))->placeholder('Banco')->rules('required|regex:/^[A-z]/|min:6', [
                'regex' => 'Este campo no acepta caracteres especiales como punto, coma, punto y coma, etc.',
                'min' => 'Mínimo debe tener 6 carácteres',
            ]);
            $form->text('CuentaBancaria', __('Cuenta Bancaria'))->placeholder('Cuenta Bancaria')->rules('required|regex:/^[0-9]/|min:6', [
                'regex' => 'Este campo no acepta caracteres especiales como punto, coma, punto y coma, etc.',
                'min' => 'Mínimo debe tener 6 carácteres',
            ]);
            $form->text('ClabeBanco', __('Clabe Bancaria'))->placeholder('Clabe Bancaria')->rules('required|min:8', [
                'regex' => 'Este campo no acepta caracteres especiales como punto, coma, punto y coma, etc.',
                'min' => 'Mínimo debe tener 8 carácteres',
            ]);
            $form->text('Correo', __('Correo'))->placeholder('Correo')->rules('required|regex:/^[A-z]/|min:8', [
                'regex' => 'Este campo no acepta caracteres especiales como punto, coma, punto y coma, etc.',
                'min' => 'Mínimo debe tener 8 carácteres',
            ]);
            $form->date('FechaNacimiento', __('Fecha de Nacimiento'))->default(date('Y-m-d'))->placeholder('Fecha de Entrada')->required();
            $form->radio('Sede', __('Sede'))
                ->options(['AICM' => 'AICM', 'AIFA' => 'AIFA', 'AICM/AIFA' => 'AICM/AIFA'])
                ->rules('required|in:AICM,AIFA,AICM/AIFA', [
                    'in' => 'Este campo únicamente acepta las opciones: AICM, AIFA o AICM/AIFA.',
                ])
                ->help('Selecciona la sede del empleado: AICM, AIFA o AICM/AIFA');

            $form->radio('Estatus', __('Estatus'))
                ->options(['Activo' => 'Activo', 'Inactivo' => 'Inactivo'])
                ->rules('required|in:Activo,Inactivo', [
                    'in' => 'Este campo únicamente acepta las palabras "Activo" y/o "Inactivo".',
                ])
                ->help('Selecciona el estado del empleado: Activo o Inactivo');
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
