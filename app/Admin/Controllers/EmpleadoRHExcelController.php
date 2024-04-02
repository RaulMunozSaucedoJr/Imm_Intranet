<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Post\BatchRestore;
use App\Admin\Actions\Post\Restore;
use App\Admin\Actions\Replicate;
use App\Imports\EmpleadoRHImport;
use Maatwebsite\Excel\Facades\Excel;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\EmpleadoRHExcel;

class EmpleadoRHExcelController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Carga Masiva Colaboradores';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new EmpleadoRHExcel());

        $grid->sortable();
        $grid->enableHotKeys();
        $grid->fixColumns(3, -1);
        $grid->enableHotKeys();
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
        $show = new Show(EmpleadoRHExcel::findOrFail($id));

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
        $form = new Form(new EmpleadoRHExcel());

        $form->tab('Registro Masivo', function ($form) {
            $form->file('excel_file', __('Archivo Excel'))->help('Este campo únicamente acepta archivos en formato .xlsx')->rules('required|mimes:xlsx');
            $form->saving(function (Form $form) {
                $file = $form->excel_file;
                if ($file) {
                    $import = new EmpleadoRHImport();
                    Excel::import($import, $file);
                    return false;
                }
            });
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
