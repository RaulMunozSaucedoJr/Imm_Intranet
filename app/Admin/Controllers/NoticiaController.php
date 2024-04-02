<?php

namespace App\Admin\Controllers;

use App\Mail\Noticias;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use OpenAdmin\Admin\Admin;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Noticia;

class NoticiaController extends AdminController
{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Noticias';

    /**F
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Noticia());
        $grid->enableHotKeys();
        $grid->quickSearch(['Titulo', 'Descripcion', 'Fecha', 'usuario']);
        $grid->selector(function (Grid\Tools\Selector $selector) {
            $selector->select('Tema', 'Filtrar por:', [
                'Importaciones' => 'Importaciones',
                'Exportaciones' => 'Exportaciones',
            ]);
        });

        $grid->filter(function ($filter) {
            $filter->column(12 / 1, function ($filter) {
                $filter->like('Titulo', 'Titulo');
            });
            $filter->column(12 / 1, function ($filter) {
                $filter->like('Tema', 'Tema');
            });
            $filter->column(12 / 1, function ($filter) {
                $filter->like('usuario', 'Autor');
            });
        });

        $grid->column('id', __('Id'))->filter()->help('Filtrar por I.D.');
        $grid->column('Titulo', __('Titulo'))->filter('like')->help('Filtrar por Título');
        $grid->column('Tema')->filter('like')->help('Filtrar por Tema');
        $grid->column('Descripcion', __('Descripcion'))->filter('like')->help('Filtrar por Descripción')->limit(30);
        $grid->column('Fecha', __('Fecha'))->filter('date')->help('Filtrar por Fecha de Registro');
        $grid->column('Image', __('Imagen'))->display(function ($image) {
            $url = url('uploads/' . $image);
            return
                "<a class='btn btn-sm btn-info' type='button' href='{$url}' target='_blank'>
                    Ver Imagen
                </a>";
        });
        $grid->column('usuario', __('Autor'))->filter('like')->color('#1c1c1c')->help('Filtrar por Autor')->display(function ($title) {
            return "<strong style='color:#1c1c1c'>$title</strong>";
        });

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
        $show = new Show(Noticia::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('usuario', __('Usuario'));
        $show->field('Titulo', __('Titulo'));
        $show->field('Tema', __('Tema'));
        $show->field('Descripcion', __('Descripcion'));
        $show->field('Fecha', __('Fecha'));
        $show->field('Image', __('Image'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

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

        Admin::style('
            li{
                color:#1c1c1c;
                font-weight:bold;
                padding:.2rem;
                font-size:1rem;
            }
        ');

        $form = new Form(new Noticia());

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
                                    En los campos (Título y Descripción) NO podrá ingresar carácteres especiales como: (comillas dobles/simples, punto y coma / dos puntos, guión medio y bajo entre otros)
                                </li>
                                <li>
                                    Los campos sombreados NO se pueden modificar.
                                </li>
                                <li>
                                    En el campo (Tema) podrá buscar y elegir un tema y/o escribir el la opción que deseé (obviamente dentro de las dos opciones disponibles únicamente).
                                </li>
                                <li>
                                    Una vez registrada la noticia se enviará al correo distribuidor de la empresa y les llegará a todos aquellos empleados que tengan correo empresarial. En caso de que usted no tenga correo empresarial, podrá ver la noticia en el panel principal de la immtranet.
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            ');
        });

        $form->column(5 / 1, function ($form) {
            $userName = Auth::user()->name;
            $form->text('usuario', __('Autor'))->readonly()->value($userName);

            $form->text('Titulo', __('Titulo'))->placeholder('Título')->rules('required|regex:/^[A-z]/|min:8|unique:noticias,Titulo', [
                'regex' => 'Este campo no acepta caracteres especiales como punto, coma, punto y coma, etc.',
                'min' => 'Mínimo debe tener 8 caracteres',
                'unique' => 'Ya hay una noticia con ese mismo título.',
            ])->autofocus();
            $form->text('Tema', 'Tema')->required()->placeholder('Tema');

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
        });

        $form->column(7 / 1, function ($form) {
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
            $form->textarea('Descripcion', __('Descripcion'))
                ->placeholder('Descripción')
                ->rules('required|regex:/^[A-z]/|min:8|unique:noticias,Descripcion', [
                    'regex' => 'Este campo no acepta caracteres especiales como punto, coma, punto y coma, etc.',
                    'min' => 'Mínimo debe tener 8 caracteres',
                    'unique' => 'Ya hay una noticia con esa misma descripción.',
                ]);
        });

        $form->confirm('¿Está seguro que desea registrar esta información？');

        $form->saving(function (Form $form) {
            $userId = Auth::id();
            $form->model()->user_id = $userId;
            $mailData = [
                'Titulo' => $form->Titulo,
                'Descripcion' => $form->Descripcion,
                'Tema' => $form->Tema,
                'Fecha' => $form->Fecha,
                'user' => Auth::user()->user,
            ];

            $mail = new Noticias($mailData);
            Mail::to('desarrollo@interpuerto.com')->send($mail);
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
