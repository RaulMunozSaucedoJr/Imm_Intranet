<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Post\BatchRestore;
use App\Admin\Actions\Post\Restore;
use App\Admin\Actions\Replicate;
use App\Mail\MailReservaciones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Reservacion;

class ReservacionController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Reservacion';

    public function dialog()
    {
        $this->question('¿Estás seguro de copiar esta fila?', 'Esto copiará todos los datos en una nueva entidad', ['icon' => 'question', 'confirmButtonText' => 'Sí']);
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Reservacion());

        $grid;
        $grid->enableHotKeys();
        $grid->fixColumns(3, -1);
        $grid->quickSearch(['Empleado', 'Sede', 'Sala']);
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
                'Aeropuerto Internacional de la Ciudad de Mexico' => 'Aeropuerto Internacional de la Ciudad de Mexico',
                'Aeropuerto Internacional Felipe Angeles' => 'Aeropuerto Internacional Felipe Angeles',
            ]);
        });
        $grid->actions(function ($actions) {
            $actions->add(new Replicate());
        });

        $grid->column('id', __('Id'))->filter();
        $grid->column('Empleado', __('Empleado'))->filter('like')->help('Es quien realiza el registro');
        $grid->column('Sede', __('Sede'))->filter('like')->help('Es la sede en la que se encuentra la sala');
        $grid->column('Sala', __('Sala'))->filter('like')->help('Es la sala en la que planea hacer la reunión');
        $grid->column('Descripcion', __('Descripcion'))->filter('like')->help('Es el motivo del porque se realizó la reservación');
        $grid->column('Fecha', __('Fecha'))->filter('date')->help('Es la fecha en la que se esta reservando la');
        $grid->column('HoraInicial', __('Hora Entrada'))->filter('like')->help('Es la hora en que se realiza la reservación');
        $grid->column('HoraFinal', __('Hora Salida'))->filter('like')->help('Es la hora en que termina la reservación.');
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
        $grid->column('deleted_at', __('Eliminado el'))->filter('range', 'datetime')

        ->display(function ($updated_at) {
        return date('d/m/Y H:i:s', strtotime($updated_at));
        });

        $grid->footer(function ($query) {
        $totalReservations = $query->count();
        return "<div style='padding: 10px;'>Total Reservaciones: $totalReservations</div>";
        });
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
        $show = new Show(Reservacion::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('Empleado', __('Empleado'));
        $show->field('Sede', __('Sede'));
        $show->field('Sala', __('Sala'));
        $show->field('Descripcion', __('Descripcion'));
        $show->field('Fecha', __('Fecha'));
        $show->field('HoraInicial', __('HoraInicial'));
        $show->field('Horafinal', __('Horafinal'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Reservacion());

        $userName = Auth::user()->name;

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
                                Los campos marcados con <strong>*</strong> son obligatorios.
                            </li>
                            <li>
                                Se le mostrará una alerta indicandole el motivo del error y en que campo se encuentra dicho error.
                            </li>
                            <li>
                                Si se realiza una reservación y pasa alguno de los siguientes escenarios, esta no se podrá realizar:
                            </li>
                            <li>
                                Si la sala, el día y horario se encuentran registrados, no se podrá realizar una reservacion con esos campos, por lo que tendrá que cambiar alguna de estas tres opciones.
                            </li>
                            <li>
                                La reservación de la sala NO se puede hacer al día actual.
                            </li>
                            <li>
                                La hora de entrada debe ser igual o mayor a las 09:00 a.m. y la de salida igual o menor a las 17:00 p.m.
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        ');

        $form->text('Empleado', __('Empleado'))->readonly()->value($userName);
        $form->select('Sede', __('Sedes'))->options(function ($name) {
            $sedes = DB::table('sedes')->pluck('sede', 'sede');
            return $sedes;
        });
        $form->select('Sala', __('Salas'))->options(function ($name) {
            $salas = DB::table('salas')->pluck('salas', 'salas');
            return $salas;
        });
        $form->textarea('Descripcion', __('Descripcion'))->placeholder('Descripción')->rules('required|regex:/[A-z]/|min:10', [
            'regex' => 'Este campo no acepta caracteres especiales que no sean punto, coma, punto y coma, etc.',
            'min' => 'Mínimo debe tener 10 carácteres',
        ]);
        $form->date('Fecha', __('Fecha'))
            ->default(date('Y-m-d'))
            ->rules([
                'required',
                'date',
                'after_or_equal:' . now()->format('Y-m-d'), // Asegurar que la fecha sea igual o posterior al día actual
                function ($attribute, $value, $fail) {
                    if ($value == now()->format('Y-m-d')) {
                        $fail('No puedes seleccionar el día actual, tiene que ser de 1 a 3 días con anticipación.');
                    }

                    $sede = request()->input('Sede');
                    $sala = request()->input('Sala');
                    $userId = Auth::id();
                    $existingReservation = Reservacion::where('Fecha', $value)
                        ->where('user_id', $userId)
                        ->where('Sede', '=', $sede)
                        ->where('Sala', '=', $sala)
                        ->exists();
                    if ($existingReservation) {
                        $fail('Ya existe una reservación para esta fecha, sede y sala.');
                    }
                },
            ]);

        $form->time('HoraInicial', 'Hora Entrada')
            ->rules([
                'required',
                function ($attribute, $value, $fail) {
                    if ($value < '09:00' || $value > '17:00') {
                        $fail('La hora de entrada debe ser entre las 9:00 y las 17:00.');
                    }
                },
            ]);

        $form->time('HoraFinal', 'Hora Salida')
            ->rules([
                'required',
                function ($attribute, $value, $fail) {
                    if ($value < '09:00' || $value > '17:00') {
                        $fail('La hora de salida debe ser entre las 9:00 y las 17:00.');
                    }
                },
            ]);

        $form->confirm('¿Está seguro que desea registrar esta información？');

        $form->saving(function (Form $form) {
            $userId = Auth::id();
            $form->model()->user_id = $userId;
            $destinatario = 'desarrollo@interpuerto.com';
            $copia = 'raulmunozsaucedo@hotmail.com';

            // Send the email
            Mail::to($destinatario)
                ->cc($copia)
                ->send(new MailReservaciones(
                    $form->Empleado,
                    $form->Sede,
                    $form->Sala,
                    $form->Descripcion,
                    $form->Fecha,
                    $form->HoraInicial,
                    $form->HoraFinal
                ));
        });

        return $form;
    }
}
