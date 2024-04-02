<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Post\BatchRestore;
use App\Admin\Actions\Post\Restore;
use App\Imports\UsuarioRHExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\User;

class UsuarioRHExcelController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Carga Masiva Usuarios';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());

        $grid->enableHotKeys();
        $grid->fixColumns(3, -1);
        $grid->enableHotKeys();
        //$grid->quickSearch(['NumeroEmpleado', 'Empleado', 'Curp', 'Sede']);
        /*
        $grid->actions(function ($actions) {
        $actions->add(new Replicate());
        });
        $grid->actions(function ($actions) {
        $actions->add(new Replicate());
        });
         */

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
            $selector->select('headquarter', 'Sede', [
                'AICM' => 'AICM',
                'AIFA' => 'AIFA',
            ]);
        });

        $grid->column('id', __('Id'));
        $grid->column('employeeNumber', __('Numero de Empleado'))->filter('like');
        $grid->column('username', __('Usuario'))->filter('like');
        $grid->column('name', __('Nombre'))->filter('like');
        $grid->column('entryDate', __('Fecha de Ingreso'))->filter('like');
        $grid->column('periodOne', __('Periodo 2021-2022'))->filter('like');
        $grid->column('periodTwo', __('Periodo 2022-2023'))->filter('like');
        $grid->column('periodThree', __('Periodo 2023-2024'))->filter('like');
        $grid->column('totalDays', __('Días Totales de Vacaciones'))->filter('like');
        $grid->column('area', __('Área'))->filter('like');
        $grid->column('boss', __('Jefe Directo'))->filter('like');
        $grid->column('headquarter', __('Sede'))->filter('like');
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
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('employeeNumber', __('Numero de Empleado'));
        $show->field('username', __('Usuario'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User());

        $form->tab('Registro Masivo', function ($form) {
            $form->file('excel_file', __('Archivo Excel'))->help('Este campo únicamente acepta archivos en formato .xlsx')->rules('required|mimes:xlsx');
            $form->saving(function (Form $form) {
                $file = $form->excel_file;
                if ($file) {
                    $import = new UsuarioRHExcelImport();
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
