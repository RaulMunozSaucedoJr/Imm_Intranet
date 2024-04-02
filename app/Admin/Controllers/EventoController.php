<?php

namespace App\Admin\Controllers;

use App\Mail\Eventos;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Evento;

class EventoController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Evento';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Evento());
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
                $filter->like('Fecha', 'Fecha');
            });
            $filter->column(12 / 1, function ($filter) {
                $filter->like('Sede', 'Sede');
            });
            $filter->column(12 / 1, function ($filter) {
                $filter->like('usuario', 'Autor');
            });
        });
        $grid->column('id', __('Id'))->filter()->help('Filtrar por I.D.');
        $grid->column('Titulo', __('Titulo'))->filter('like')->help('Filtrar por Título');
        $grid->column('Fecha', __('Fecha'))->filter('date')->help('Filtrar por Fecha');
        $grid->column('Sede')->filter('like')->help('Filtrar por Sede');
        $grid->column('Descripcion', __('Descripcion'))->filter('like');
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
        $show = new Show(Evento::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('usuario', __('Registró'));
        $show->field('Titulo', __('Titulo'));
        $show->field('Fecha', __('Fecha'));
        $show->field('Sede', __('Sede'));
        $show->field('Descripcion', __('Descripcion'));
        $show->field('Image', __('Image'));
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
        $form = new Form(new Evento());

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
                                    Una vez registradó el evento, se enviará al correo distribuidor de la empresa y les llegará a todos aquellos empleados que tengan correo empresarial. En caso de que usted no tenga correo empresarial, podrá ver el evento registrado en el panel principal de la immtranet.
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
            $form->text('usuario', __('Autor'))->readonly()->value($userName);
            $form->text('Titulo', __('Titulo'))->placeholder('Título')->rules('required|regex:/^[A-z]/|min:8|unique:eventos,Titulo', [
                'regex' => 'Este campo no acepta caracteres especiales como punto, coma, punto y coma, etc.',
                'min' => 'Mínimo debe tener 8 caracteres',
                'unique' => 'Ya hay una noticia con ese mismo título.',
            ]);
            $form->date('Fecha', __('Fecha'))
                ->default(date('Y-m-d'))
                ->required()
                ->attribute('style', 'width: 100%')
                ->rules([
                    'required',
                    'date',
                    function ($attribute, $value, $fail) {
                        $fechaActual = Carbon::now()->format('Y-m-d');
                        // Verificar si la fecha es anterior al día actual
                        if ($value < $fechaActual) {
                            $fail(__('La fecha no puede ser anterior al día actual.'));
                        }
                    },
                ]);
            $form->select('Sede', __('Sede'))->options(function ($name) {
                $users = DB::table('sedes')->pluck('sede', 'sede');
                return $users;
            })->required();
        });

        $form->column(6 / 1, function ($form) {
            $form->image('Image', __('Imagen'))->required()
                ->rules([
                    'mimes:jpg,png', // Asegura que el archivo tenga extensión .jpg o .png
                    function ($attribute, $value, $fail) {
                        // Verificar si el nombre del archivo tiene más de una extensión
                        $filename = pathinfo($value->getClientOriginalName(), PATHINFO_FILENAME);
                        $extension = $value->getClientOriginalExtension();
                        if (preg_match('/\.(?!.{1,4}\bjpg|png\b)[^.]+$/i', $filename)) {
                            $fail('¡El archivo subido NO debe tener una doble extensión!');
                        }
                    },
                ], [
                    'mimes' => 'Únicamente acepta imágenes tipo .jpg y/o .png',
                ]);
            $form->textarea('Descripcion', __('Descripcion'))->placeholder('Descripción')->rules('required|regex:/[A-z]/|min:10', [
                'regex' => 'Este campo no acepta caracteres especiales que no sean punto, coma, punto y coma, etc.',
                'min' => 'Mínimo debe tener 10 carácteres',
            ]);

        });

        $form->confirm('¿Está seguro que desea registrar esta información？');

        $form->saving(function (Form $form) {
            $mailData = [
                'Titulo' => $form->Titulo,
                'Fecha' => $form->Fecha,
                'Sede' => $form->Sede,
                'Descripcion' => $form->Descripcion,
                'user' => Auth::user()->name,
            ];
            $mail = new Eventos($mailData);
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
