<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Post\BatchRestore;
use App\Admin\Actions\Post\Restore;
use App\Admin\Actions\Replicate;
use App\Mail\TicketsMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Ticket;

class TicketController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Ticket';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Ticket());
        $grid->model()->when(Auth::user()->name !== 'Mantenimiento&TI', function ($query) {
            $query->where('user_id', Auth::id());
        });
        $grid->enableHotKeys();
        $grid->fixColumns(3, -1);
        $grid->enableHotKeys();
        $grid->quickSearch(['Titulo', 'Descripcion', 'Fecha']);
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
            $selector->select('Estatus', 'Estatus', [
                'Abierto' => 'Abierto',
                'En Proceso' => 'En Proceso',
                'Rechazado' => 'Rechazado',
                'Cerrado' => 'Cerrado',
            ]);
        });
        $grid->actions(function ($actions) {
            $actions->add(new Replicate());
        });

        $grid->column('id', __('Id'));
        $grid->column('Empleado', __('Empleado'))->filter('like');
        $grid->column('Fecha', __('Fecha'))->date()->filter('range', 'date');
        $grid->column('TipoProblema', __('Tipo de Problema'))->filter('like');
        $grid->column('Sede', __('Sede'))->filter('like')
            ->select(['Aeropuerto Internacional de la Ciudad de Mexico' => 'Aeropuerto Internacional de la Ciudad de Mexico', 'Aeropuerto Internacional Felipe Angeles' => 'Aeropuerto Internacional Felipe Angeles']);
        $grid->column('Prioridad', __('Prioridad'))->filter('like')
            ->select(['Alta' => 'Alta', 'Media' => 'Media', 'Baja' => 'Baja']);
        $grid->column('PeriodoDeTiempo', __('Periodo De Tiempo'))->filter('like');
        $grid->column('Locacion', __('Locación'))->filter('like');
        $grid->column('Piso', __('Piso'))->filter('like');
        $grid->column('Departamento', __('Departamento'))->filter('like');
        $grid->column('Sistema', __('Sistema'))->filter('like');
        $grid->column('Modulo', __('Modulo'))->filter('like');
        $grid->column('Descripcion', __('Descripcion'))->filter('like');
        $grid->column('Evidencia', __('Evidencia'))->display(function ($image) {
            $url = url('uploads/' . $image);
            return
                "<a class='btn btn-sm btn-info' type='button' href='{$url}' target='_blank'>
                    Ver Imagen
                </a>";
        });
        $grid->column('Estatus')->select(['En proceso' => 'En proceso', 'Abierto' => 'Abierto', 'Rechazado' => 'Rechazado', 'Cerrado' => 'Cerrado'])->filter([
            'En proceso' => 'En proceso',
            'Abierto' => 'Abierto',
            'Rechazado' => 'Rechazado',
            'Cerrado' => 'Cerrado',
        ]);
        $grid->column('RazonRechazo', __('Razon de Rechazo'))->display(function ($razonRechazo) {
            // Obtener el modelo actual
            $ticket = $this->getModel();

            // Verificar si el modelo tiene la propiedad "Estatus"
            if ($ticket->Estatus === 'Rechazado' || $ticket->Estatus === 'Cerrado') {
                return $razonRechazo;
            } else {
                return ''; // Mostrar vacío si el estatus no es "Rechazado" o "Cerrado"
            }
        });
        $grid->column('Calificacion', __('Calificacion'))->filter('like');
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
        $show = new Show(Ticket::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('Empleado', __('Empleado'));
        $show->field('Fecha', __('Fecha'));
        $show->field('TipoProblema', __('Tipo de Problema'));
        $show->field('Sede', __('Sede'));
        $show->field('Prioridad', __('Prioridad'));
        $show->field('PeriodoDeTiempo', __('Periodo de Tiempo'));
        $show->field('TipoPeriodo', __('Tipo de Periodo'));
        $show->field('Locacion', __('Locacion'));
        $show->field('Piso', __('Piso'));
        $show->field('Departamento', __('Departamento'));
        $show->field('Sistema', __('Sistema'));
        $show->field('Modulo', __('Modulo'));
        $show->field('Descripcion', __('Descrpcion'));
        $show->field('Evidencia', __('Evidencia'));
        $show->field('Comentario', __('Comentario'));
        $show->field('Estatus', __('Estatus'));
        $show->field('RazonRechazo', __('Razon de Rechazo'));
        $show->field('Calificacion', __('Calificacion'));
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
        $form = new Form(new Ticket());

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
                                Los campos marcados con <strong>* son obligatorios.</strong>
                            </li>
                            <li>
                                Si se edita un registro y este tiene información exactamente igual a otro ya registrado, <strong>no se podrá guardar/registrar.</strong>
                            </li>
                            <li>
                                Se le mostrará una alerta indicandole el motivo del error y en que campo se encuentra dicho error.
                            </li>
                            <hr/>
                            <li>

                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        ');

        $form->tab('Información básica', function ($form) {
            $userName = Auth::user()->name;
            $form->text('Empleado', __('Empleado'))->readonly()->value($userName)->required();
            $form->datetime('Fecha', __('Fecha'))->default(date('Y-m-d'))->placeholder('Fecha de Registro')->required();
            $form->select('TipoProblema', __('Tipo de Problema'))->options(function ($name) {
                $users = DB::table('problemas')->pluck('problemas', 'problemas');
                return $users;
            })->required();
        })->tab('Tipo de Problema & Tiempo de Resolución', function ($form) {

            $form->select('Prioridad', 'Prioridad')
                ->options([
                    '' => 'Seleccione una proridad',
                    'Alta' => 'Alta',
                    'Baja' => 'Baja',
                    'Media' => 'Media',
                ])
                ->when('Alta', function (Form $form) {
                    $form->text('PeriodoDeTiempo', 'Periodo De Tiempo')->value('1 Hora')->readonly();
                })->when('Media', function (Form $form) {
                $form->text('PeriodoDeTiempo', 'Periodo De Tiempo')->value('2 Horas')->readonly();
            })->when('Baja', function (Form $form) {
                $form->text('PeriodoDeTiempo', 'Periodo De Tiempo')->value('1 Día')->readonly();
            })->required();

        })->tab('Sede & Departamento', function ($form) {
            $form->select('Sede', __('Sede'))->options(function ($name) {
                $users = DB::table('sedes')->pluck('sede', 'sede');
                return $users;
            })->required();
            $form->select('Locacion', __('Locacion'))->options(function ($name) {
                $users = DB::table('ubicaciones')->pluck('ubicacion', 'ubicacion');
                return $users;
            })->required();
            $form->select('Piso', __('Piso'))->options(function ($name) {
                $users = DB::table('pisos')->pluck('piso', 'piso');
                return $users;
            })->required();
            $form->select('Departamento', __('Departamento'))->options(function ($name) {
                $users = DB::table('departamentos')->pluck('departamento', 'departamento');
                return $users;
            })->required();
        })->tab('Sistema & Modúlo', function ($form) {
            $form->select('Sistema', __('Sistema'))->options(function ($name) {
                $users = DB::table('sistemas')->pluck('sistema', 'sistema');
                return $users;
            })->required();
            $form->select('Modulo', __('Modulo'))->options(function ($name) {
                $users = DB::table('modulos')->pluck('modulo', 'modulo');
                return $users;
            });
            $form->textarea('Descripcion', __('Descripcion'))->required();
            $form->select('Estatus', __('Estatus'))->options(function ($name) {
                $users = DB::table('ticket_estatus')->pluck('Estatus', 'Estatus');
                return $users;
            })->required();
        })->tab('Evidencia', function ($form) {
            $form->image('Evidencia', __('Evidencia'))->removable();
            $form->confirm('¿Está seguro que desea registrar esta información？');

        });

        $form->saving(function (Form $form) {
            $userId = Auth::id();
            $form->model()->user_id = $userId;
            $mailData = [
                'Empleado' => $form->Empleado,
                'Fecha' => $form->Fecha,
                'TipoProblema' => $form->TipoProblema,
                'Sede' => $form->Sede,
                'Prioridad' => $form->Prioridad,
                'PeriodoDeTiempo' => $form->PeriodoDeTiempo,
                'Locacion' => $form->Locacion,
                'Piso' => $form->Piso,
                'Departamento' => $form->Departamento,
                'Sistema' => $form->Sistema,
                'Modulo' => $form->Modulo,
                'Descripcion' => $form->Descripcion,
                'Estatus' => $form->Estatus,
                'RazonRechazo' => $form->RazonRechazo,
                'Calificacion' => $form->Calificacion,
                'user' => Auth::user()->user,
            ];

            $mail = new TicketsMail($mailData);
            Mail::to('raulmunozsaucedo@hotmail.com')->send($mail);
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
