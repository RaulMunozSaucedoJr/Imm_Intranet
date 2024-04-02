<?php

namespace App\Admin\Controllers;

use App\Mail\AprobadasPermiso;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Permiso;

class PermisoController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Permiso';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Permiso());
        $grid->model()->when(Auth::user()->name !== 'HHRR', function ($query) {
            $query->where('user_id', Auth::id());
        });
        $grid->enableHotKeys();
        $grid->enableHotKeys();
        $grid->quickSearch(['Titulo', 'Descripcion', 'Fecha']);
        /*
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
        $grid->actions(function ($actions) {
        $actions->add(new Replicate());
        });
         */

        $grid->selector(function (Grid\Tools\Selector $selector) {
            $selector->select('Estatus', 'Filtrar por:', [
                'Por Aprobar' => 'Por Aprobar',
                'Rechazadas' => 'Rechazadas',
                'Aprobadas' => 'Aprobadas',
            ]);
        });

        $grid->column('id', __('Id'))->help('Filtrar por I.D.')->filter('like');
        $grid->column('Empleado', __('Empleado'))->help('Filtrar por Empleado')->filter('like');
        $grid->column('Fecha', __('Fecha'))->filter('date')->help('Filtrar por Fecha');
        $grid->column('Jefe', __('Jefe'))->help('Filtrar por Jefe Inmediato')->filter('like');
        $grid->column('Motivo', __('Motivo'))->help('Filtrar por Motivo')->filter('like');
        $grid->column('Descripcion', __('Descripcion'))->filter('like');
        $grid->column('Estatus')->filter('like')
            ->select(['Aprobadas' => 'Aprobadas', 'Rechazadas' => 'Rechazadas']);
        $grid->column('Image', __('Evidencia'))->display(function ($image) {
            $url = url('uploads/' . $image);
            return
                "<a class='btn btn-sm btn-info' type='button' href='{$url}' target='_blank'>
                    Ver Evidencia
                </a>";
        });
        /*
        $grid->column('created_at', __('Creado el'))
        ->filter('range', 'datetime')

        ->display(function ($created_at) {
        return date('d/m/Y H:i:s', strtotime($created_at));
        });

        $grid->column('updated_at', __('Actualizado el'))
        ->filter('range', 'datetime')

        ->display(function ($updated_at) {
        return date('d/m/Y H:i:s', strtotime($updated_at));
        });
        $grid->column('deleted_at', __('Eliminado el'))->filter('range', 'datetime');

         */
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
        $show = new Show(Permiso::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('Empleado', __('Empleado'));
        $show->field('Jefe', __('Jefe'));
        $show->field('Motivo', __('Motivo'));
        $show->field('Descripcion', __('Descripcion'));
        $show->field('Image', __('Image'));
        /*
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('deleted_at', __('Deleted at'));
         */

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Permiso());

        $form->column(12 / 1, function ($form) {
            $form->html('
            <div class="accordion" id="formAccordion">
                <div class="accordion-item">
                    <h4 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <b>INFORMACIÓN</b>
                        </button>
                    </h4>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#formAccordion">
                        <div class="accordion-body">
                            <ol>
                                <li>
                                    Los campos marcados con <strong class="text-danger">*</strong> son obligatorios.
                                </li>
                                <li>
                                    Si se edita un registro y este tiene información exactamente igual a otro ya registrado, no se podrá guardar/registrar.
                                </li>
                                <li>
                                    Se le mostrará una alerta indicandole el motivo del error y en que campo se encuentra dicho error.
                                </li>
                                <li>
                                    Los permisos registrados se enviaran al correo electrónico de su jefe de área.
                                </li>
                                <li>
                                    Se le pide estar atento a esta sección para ver si se les ha aprobado ó rechazado su permiso.
                                </li>
                                <li>
                                    Se le pide adjuntar evidencia de el porque esta solicitando su permiso.
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            ');
        });

        $form->column(6 / 1, function ($form) {
            $userName = Auth::user()->name;
            $userArea = Auth::user()->area;
            $form->text('Empleado', __('Empleado'))->readonly()->value($userName)->required();
            $form->select('area', 'Área')
                ->options([
                    $userArea => $userArea,
                ])
                ->value($userArea)
                ->readonly()
                ->required();
            if ($userArea === 'RH-Intendencia-Chóferes-Asistente-Administrativo') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Jordana Pérez')
                    ->readonly()
                    ->required();
            } elseif ($userArea === 'Auditoría') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Gabriela Lozanos')
                    ->readonly()

                    ->required();
            } elseif ($userArea === 'Logística') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Esteban Hernández')
                    ->readonly()

                    ->required();
            } elseif ($userArea === 'Atenciónalcliente') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Teresa Fuentes')
                    ->readonly()

                    ->required();
            } elseif ($userArea === 'Mantenimiento-TI') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Gilberto Silva')
                    ->readonly()

                    ->required();
            } elseif ($userArea === 'Operaciones-Gerencias') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Fernando González')
                    ->readonly()

                    ->required();
            } elseif ($userArea === 'Operaciones') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Cristian Rizo')
                    ->readonly()

                    ->required();
            } elseif ($userArea === 'Transferencias') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Ericka Hidalgo')
                    ->readonly()

                    ->required();
            } elseif ($userArea === 'Desarrollo') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Raul')
                    ->readonly()

                    ->required();
            }
            $form->date('Fecha', __('Fecha'))->default(date('Y-m-d'))->rules('required|unique:eventos,Fecha,NULL,id,user_id,' . Auth::id(), [
                'unique' => 'Ya existe un evento con la misma fecha.',
            ])->attribute('style', 'width: 100%');
            $form->text('Motivo', __('Motivo'))->required();
        });

        $form->column(6 / 1, function ($form) {

            $form->textarea('Descripcion', __('Descripcion'))->required();
            $form->select('Estatus', 'Estatus')
                ->options([
                    '' => 'Seleccione un estatus',
                    'Por Aprobar' => 'Por Aprobar',
                    'Aprobadas' => 'Aprobadas',
                    'Rechazadas' => 'Rechazadas',
                ])->when('Rechazadas', function (Form $form) {
                $form->textarea('rejectionReason', 'Razón de Rechazo')->value(' ')->readonly();
            })->value('Por Aprobar')->readonly();
            $form->image('Image', __('Evidencia'))->required();
        });

        $form->confirm('¿Está seguro que desea registrar esta información？');

        $form->saving(function (Form $form) {
            if ($form->isUpdate() && $form->isOnlyUpdatingField('Estatus')) {
                return;
            }
        });

        $form->saving(function (Form $form) {
            $userId = Auth::id();
            $form->model()->user_id = $userId;
            $jefe = $form->input('Jefe');
            $destinatario = '';
            switch ($jefe) {
                case 'Mónica Flores':
                    $destinatario = 'monica.flores@braniff.com';
                    break;
                case 'Gabriela Lozanos':
                    $destinatario = 'gabriela.lozano@interpuerto.com';
                    break;
                case 'Esteban Hernández':
                    $destinatario = 'gabriela.lozano@interpuerto.com';
                    break;
                case 'Teresa Fuentes':
                    $destinatario = 'teresa.fuentes@interpuerto.com';
                    break;
                case 'Gilberto Silva':
                    $destinatario = 'gilberto.silva@interpuerto.com';
                    break;
                case 'Fernando González':
                    $destinatario = 'fernando.gonzalez@interpuerto.com';
                    break;
                case 'Cristian Rizo':
                    $destinatario = 'cristian.rizo@interpuerto.com';
                    break;
                case 'Ericka Hidalgo':
                    $destinatario = 'erika.hidalgo@interpuerto.com';
                    break;
                case 'Jordana Pérez':
                    $destinatario = 'jordana.perez@interpuerto.com';
                    break;
                case 'Raul':
                    $destinatario = 'raulmunozsaucedo@hotmail.com';
                    break;
                default:
                    $destinatario = 'desarrollo@interpuerto.com';
            }

            if ($form->isUpdate() && $form->isOnlyUpdatingField('Estatus')) {
                $userEmail = User::where('id', Auth::id())->value('email');
                $Estatus = $form->input('Estatus');
                if ($Estatus === 'Aprobadas') {
                    Mail::to($destinatario)->send(new AprobadasPermiso($Estatus));
                    if (!empty($userEmail)) {
                        Mail::to($userEmail)->send(new AprobadasPermiso($Estatus));
                    }
                }
            }

        });

        $form->footer(function ($footer) {
            $footer->disableReset();
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
        });

        return $form;
    }

}
