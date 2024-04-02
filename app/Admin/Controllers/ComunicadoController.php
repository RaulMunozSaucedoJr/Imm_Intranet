<?php

namespace App\Admin\Controllers;

use App\Mail\Comunicados;
//use App\Mail\Comunicados;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use OpenAdmin\Admin\Admin;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Comunicado;

class ComunicadoController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Comunicados';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Comunicado());
        $grid->enableHotKeys();
        $grid->quickSearch(['Titulo', 'Descripcion', 'Fecha']);
        $grid->selector(function (Grid\Tools\Selector $selector) {
            $selector->select('Sede', 'Filtrar por:', [
                'Aeropuerto Internacional de la Ciudad de Mexico' => 'Aeropuerto Internacional de la Ciudad de Mexico',
                'Aeropuerto Internacional Felipe Angeles' => 'Aeropuerto Internacional Felipe Angeles',
            ]);
        });

        $grid->filter(function ($filter) {
            $filter->column(12 / 1, function ($filter) {
                $filter->like('Titulo', 'Titulo');
            });
            $filter->column(12 / 1, function ($filter) {
                $filter->like('Sede', 'Sede');
            });
            $filter->column(12 / 1, function ($filter) {
                $filter->like('usuario', 'Autor');
            });
        });

        $grid->column('id', __('Id'))->filter()->help('Filtrar por I.D.');
        $grid->column('Titulo', __('Titulo'))->filter('like')->help('Filtrar por Titulo');

        $grid->column('Sede', __('Sede'))->filter('like')->help('Filtrar por Sedes.');
        $grid->column('Descripcion', __('Descripcion'))->filter('like')->limit(30);
        $grid->column('Image', __('Imagen'))->display(function ($image) {
            $url = url('uploads/' . $image);
            return
                "<a class='btn btn-sm btn-info' type='button' href='{$url}' target='_blank'>
                    Ver Imagen
                </a>";
        });
        $grid->column('Fecha', __('Fecha'))->filter('date')->help('Filtrar por Fecha');
        $grid->column('usuario', __('Autor'))->filter('like')->color('#1c1c1c')->display(function ($title) {
            return "<strong style='color:#1c1c1c'>$title</strong>";
        })->help('Filtrar por Autor');

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
        $show = new Show(Comunicado::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('usuario', __('Usuario'));
        $show->field('Titulo', __('Titulo'));
        $show->field('Fecha', __('Fecha'));
        $show->field('Descripcion', __('Descripcion'));
        $show->field('Image', __('Image'));
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
        $form = new Form(new Comunicado());
        $userName = Auth::user()->name;
        $fechaActual = Carbon::now()->format('Y-m-d');

        Admin::style('
            li{
                color:#1c1c1c;
                font-weight:bold;
                padding:.2rem;
                font-size:1rem;
            }
        ');

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
                                    En los campos (Título, Tema y Descripción) NO podrá ingresar carácteres especiales como: (comillas dobles/simples, punto y coma / dos puntos, guión medio y bajo entre otros)
                                </li>
                                <li>
                                    Los campos sombreados NO se pueden modificar.
                                </li>
                                <li>
                                    En el campo (Tema) podrá buscar y elegir un tema y/o escribir el la opción que deseé (obviamente dentro de las dos opciones disponibles únicamente).
                                </li>
                                <li>
                                    Una vez registrado el comunicado se enviará al correo distribuidor de la empresa y les llegará a todos aquellos empleados que tengan correo empresarial. En caso de que usted no tenga correo empresarial, podrá ver la noticia en el panel principal de la immtranet.
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            ');
        });

        $form->text('usuario', __('Autor'))->readonly()->value($userName);
        $form->text('Titulo', __('Titulo'))->placeholder('Título')->rules('required|regex:/^[A-Za-zÀ-ÖØ-öø-ÿ0-9]+(?:[\s.,:;][A-Za-zÀ-ÖØ-öø-ÿ0-9]+)*$/u|min:10|max:60|unique:comunicados,Titulo', [
            'regex' => '
                Este campo ÚNICAMENTE acepta lo siguiente:
                1. Letras/Letras acentuadas.
                2. Números
                3. Punto, Coma, Punto y Coma, Doble Punto, Comillas Simples, Comillas Dobles.
                4. Únicamente un espacio en blanco entre palabras.
            ',
            'min' => 'Mínimo deben de ser 10 carácteres/letras/números.',
            'max' => 'Máximo deben de ser 60 carácteres/letras/números.',
            'unique' => 'Ya existe un comunicado con ese Titulo.',
        ]);
        $form->datetime('Fecha', __('Fecha'))
            ->format('YYYY-MM-DD HH:mm:ss')
            ->rules([
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $fechaActual = Carbon::now()->format('Y-m-d');
                    $horaLimiteFin = Carbon::parse('17:00:00');
                    // Verificar si la fecha es anterior al día actual
                    if ($value < $fechaActual) {
                        $fail(__('La fecha no puede ser anterior al día actual.'));
                    }
                    // Verificar si la hora está fuera del rango permitido
                    $horaSeleccionada = Carbon::parse($value)->format('H:i:s');
                    if ($horaSeleccionada > $horaLimiteFin) {
                        $fail(__('La hora debe estar entre las 09:00 a.m. y las 17:00 p.m.'));
                    }
                },
            ]);

        $form->select('Sede', __('Sede'))->options(function ($name) {
            $users = DB::table('sedes')->pluck('sede', 'sede');
            return $users;
        })->required();
        $form->textarea('Descripcion', __('Descripcion'))->placeholder('Descripción del comunicado.')->rules('required|regex:/^[A-Za-zÀ-ÖØ-öø-ÿ0-9]+(?:[\s.,:;][A-Za-zÀ-ÖØ-öø-ÿ0-9]+)*$/u|min:10|max:300', [
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
        $form->image('Image', __('Imagen'))
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

        $form->confirm('¿Está seguro que desea registrar esta información？');

        $form->saving(function (Form $form) {
            $userId = Auth::id();
            $form->model()->user_id = $userId;
            $mailData = [
                'Titulo' => $form->Titulo,
                'Fecha' => $form->Fecha,
                'Sede' => $form->Sede,
                'Descripcion' => $form->Descripcion,
                'user' => Auth::user()->name,
            ];

            $mail = new Comunicados($mailData);
            Mail::to('correo@gmail.com')->send($mail);
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
