<?php

namespace App\Admin\Controllers;

use App\Mail\EmpleadoDelMes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\EmpleadoMes;

class EmpleadoMesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Empleado del Mes';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new EmpleadoMes());
        $grid->enableHotKeys();
        $grid->quickSearch(['Empleado', 'Fecha', 'Descripcion']);
        $grid->selector(function (Grid\Tools\Selector $selector) {
            $selector->select('Tema', 'Filtrar por:', [
                'Empleado' => 'Empleado',
                'Autor' => 'Autor',
            ]);
        });
        $grid->filter(function ($filter) {
            $filter->column(12 / 1, function ($filter) {
                $filter->like('Empleado', 'Empleado');
            });
            $filter->column(12 / 1, function ($filter) {
                $filter->like('Fecha', 'Fecha');
            });
            $filter->column(12 / 1, function ($filter) {
                $filter->like('Autor', 'Autor');
            });
        });

        $grid->column('id', __('Id'))->filter()->help('Filtrar por I.D.');
        $grid->column('Empleado', __('Empleado'))->filter()->help('Filtrar por Empleado');
        $grid->column('Fecha', __('Fecha'))->filter('range', 'date')->help('Filtrar por Fecha');
        $grid->column('Descripcion', __('Descripcion'))->filter('like')->limit(50);
        $grid->column('Image', __('Imagen'))->display(function ($image) {
            $url = url('uploads/' . $image);
            return
                "<a class='btn btn-sm btn-info' type='button' href='{$url}' target='_blank'>
                    Ver Imagen
                </a>";
        });
        $grid->column('usuario', __('Autor'))->filter('like')->help('Filtrar por Autor');

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
        $show = new Show(EmpleadoMes::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('Empleado', __('Empleado'));
        $show->field('Fecha', __('Fecha'));
        $show->field('Descripcion', __('Descripcion'));
        $show->field('Imagen', __('Imagen'));
        $show->field('usuario', __('Usuario'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('deleted_at', __('Deleted at'));
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new EmpleadoMes());

        $form->column(12 / 1, function ($form) {
            $form->html('
            <div class="accordion" id="formAccordion">
                <div class="accordion-item">
                    <h4 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <b>INFORMACIÓN - INSTRUCCIONES</b>
                        </button>
                    </h4>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#formAccordion">
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
                                    Se le hará saber cuando un campo este vacio mediante el borde de un color rojo.
                                </li>
                                <li>
                                    Los campos sombreados NO se pueden modificar.
                                </li>
                                <li>
                                    Una vez registradó el empleado del mes, se enviará al correo distribuidor de la empresa y les llegará a todos aquellos empleados que tengan correo empresarial. En caso de que usted no tenga correo empresarial, podrá ver la noticia en el panel principal de la immtranet.
                                </li>
                                <li>
                                    El único formato aceptado en el campo Imagen, es .jpg, .png .
                                </li>
                                <li>
                                    El campo Descripción <strong>NO</strong> acepta carácteres especiales como: punto y coma / dos puntos, comillas dobles / sencillas, etc.
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
            $form->text('usuario', __('Autor'))->readonly()
                ->value($userName);
            $form->select('Empleado', __('Empleado'))->options(function ($name) {
                $users = DB::table('admin_users')->pluck('username', 'username');
                return $users;
            })->required();
            $form->date('Fecha', __('Fecha'))->default(date('Y-m-d'))->required()
                ->rules(['date', function ($attribute, $value, $fail) {
                    $empleado = request()->input('Empleado');
                    $mes = date('m', strtotime($value));
                    $year = date('Y', strtotime($value));

                    $existingRecord = EmpleadoMes::where('Empleado', $empleado)
                        ->whereMonth('Fecha', $mes)
                        ->whereYear('Fecha', $year)
                        ->exists();
                    if ($existingRecord) {
                        $fail('Ya existe un registro para este empleado en este mes.');
                    }
                }, function ($attribute, $value, $fail) {
                    $empleado = request()->input('Empleado');
                    $fechaActual = Carbon::now()->format('Y-m-d');
                    $currentMonth = date('m', strtotime($value));
                    $currentYear = date('Y', strtotime($value));

                    $startOfMonth = Carbon::create($currentYear, $currentMonth, 1, 0, 0, 0);
                    $endOfMonth = $startOfMonth->copy()->endOfMonth();

                    $existingRecord = EmpleadoMes::where('Fecha', '>=', $startOfMonth)
                        ->where('Fecha', '<=', $endOfMonth)
                        ->exists();
                    if ($existingRecord) {
                        $fail('Ya existe un registro para este mes. Debes esperar hasta el próximo mes para registrar.');
                    }

                    if ($value < $fechaActual) {
                        $fail(__('La fecha no puede ser anterior al día actual.'));
                    }
                }])->attribute('style', 'width: 100%');

        });

        $form->column(6 / 1, function ($form) {
            $form->image('Image', __('Imagen'))
                ->required()
                ->rules([
                    'mimes:jpg,png', // Asegura que el archivo tenga extensión .jpg o .png
                    function ($attribute, $value, $fail) {
                        // Verificar si el nombre del archivo tiene más de una extensión
                        $filename = pathinfo($value->getClientOriginalName(), PATHINFO_FILENAME);
                        $extension = $value->getClientOriginalExtension();
                        if (preg_match('/\.(?!.{1,4}\bjpg|png\b)[^.]+$/i', $filename)) {
                            $fail('El archivo no puede tener una doble extensión.');
                        }
                    },
                ], [
                    'mimes' => 'Únicamente acepta imágenes tipo .jpg y/o .png',
                ]);
            $form->textarea('Descripcion', __('Descripcion'))->placeholder('Descripción del porque el empleado obtiene este reconocimiento.')->rules('required|regex:/^[A-Za-zÀ-ÖØ-öø-ÿ0-9]+(?:[\s.,:;][A-Za-zÀ-ÖØ-öø-ÿ0-9]+)*$/u|min:10|max:300', [
                'regex' => '
                        Este campo ÚNICAMENTE acepta lo siguiente:
                        1. Letras/Letras acentuadas.
                        2. Números
                        3. Punto, Coma, Punto y Coma, Doble Punto, Comillas Simples, Comillas Dobles.
                        4. Únicamente un espacio en blanco entre palabras.
                    ',
                'min' => 'Mínimo deben de ser 10 carácteres/letras/números',
                'max' => 'Máximo deben de ser 300 carácteres/letras/números',
            ]);
        });

        $form->confirm('¿Está seguro que desea registrar esta información？');

        $form->saving(function (Form $form) {
            $userId = Auth::id();
            $form->model()->user_id = $userId;
            $mailData = [
                'Empleado' => $form->Empleado,
                'Fecha' => $form->Fecha,
                'Descripcion' => $form->Descripcion,
                'user' => Auth::user()->user,
            ];

            $mail = new EmpleadoDelMes($mailData);
            Mail::to('raulmunozsaucedo@hotmail.com')->send($mail);
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
