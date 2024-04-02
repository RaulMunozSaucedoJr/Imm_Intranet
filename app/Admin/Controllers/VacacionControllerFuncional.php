<?php

namespace App\Admin\Controllers;

use App\Mail\AprobadasMail;
use App\Mail\MailVacaciones;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use OpenAdmin\Admin\Admin;
use OpenAdmin\Admin\Controllers\AdminController;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Grid;
use OpenAdmin\Admin\Show;
use \App\Models\Vacacion;

class VacacionController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Vacacion';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */

    protected function grid()
    {
        $grid = new Grid(new Vacacion());

        $grid->column('id', __('Id'));

        $grid->model()->when(
            Auth::user()->name !== 'HHRR' &&
            Auth::user()->name !== 'SUPERADMIN' &&
            Auth::user()->name !== 'Gonzales Macias Fernando' &&
            Auth::user()->name !== 'Cristian' &&
            Auth::user()->name !== 'Silva Becerrril Gilberto' &&
            Auth::user()->name !== 'Raul Muñoz Saucedo',
            function ($query) {
                $query->where('user_id', Auth::id());
            }
        );

        $grid->filter(function ($filter) {
            $filter->column(1 / 2, function ($filter) {
                $filter->like('employee', 'Empleado');
            });
            $filter->column(1 / 2, function ($filter) {
                $filter->like('area', 'Área');
            });
            $filter->column(1 / 2, function ($filter) {
                $filter->like('boss', 'Jefe');
            });
            $filter->column(1 / 2, function ($filter) {
                $filter->like('status', 'Estatus');
            });
        });

        $grid->column('employee', __('Empleado'))->filter('like')->color('#1c1c1c')->display(function ($employee) {
            return "<strong'>$employee</strong>";
        });
        $grid->column('area', __('Área'))->filter('like')->color('#1c1c1c')->display(function ($area) {
            return "<strong'>$area</strong>";
        });
        $grid->column('Jefe', __('Jefe'))->filter('like')->color('#1c1c1c')->display(function ($Jefe) {
            return "<strong'>$Jefe</strong>";
        });
        $grid->column('initialDate', __('Fecha Inicial'))->filter('like')->color('#1c1c1c')->display(function ($initialDate) {
            return "<strong'>$initialDate</strong>";
        });
        $grid->column('finalDate', __('Fecha Final'))->filter('like')->color('#1c1c1c')->display(function ($finalDate) {
            return "<strong'>$finalDate</strong>";
        });

        $grid->column('remainingDays', __('Días Restantes'))->filter('like')->color('#1c1c1c')->display(function ($remainingDays) {
            return "<strong'>$remainingDays</strong>";
        });
        $grid->column('rejectionReason', __('Razón de Rechazo'))->filter('like')->color('#1c1c1c')->textarea()->display(function ($rejectionReason) {
            return "<strong'>$rejectionReason</strong>";
        });
        $grid->column('status', __('Estatus'))->radio([
            'Aprobadas' => 'Aprobadas',
            'Rechazadas' => 'Rechazadas',
        ])->color('#1c1c1c')->filter('like')->display(function ($status) {
            return "<strong>$status</strong>";
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
        $show = new Show(Vacacion::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('employee', __('Empleado'));
        $show->field('area', __('Área'));
        $show->field('Jefe', __('Jefe'));
        $show->field('initialDate', __('Fecha Inicial'));
        $show->field('finalDate', __('Fecha Final'));
        $show->field('extraInitialDate', __('Día NO laboral 1'));
        $show->field('extraFinalDate', __('Día NO laboral 2'));
        $show->field('status', __('Estatus'));
        $show->field('totalDays', __('Total de días'));
        //$show->field('color', __('Color'));
        $show->field('daysDifference', __('Días seleccionados'));
        $show->field('remainingDays', __('Días restantes'));
        $show->field('rejectionReason', __('Razón de Rechazo'));
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableDelete();
            });
        return $show;
    }

    protected function form()
    {

        $form = new Form(new Vacacion());
        $user = Auth::user();
        $previousVacation = Vacacion::where('user_id', $user->id)->latest()->first();
        $userId = Auth::id();
        $form->model()->user_id = $userId;

        if ($previousVacation) {
            $remainingDays = $previousVacation->remainingDays;
        } else {
            $remainingDays = $user->totalDays;
        }

        $form->column(12 / 1, function ($form) {
            $form->html('
                <div class="accordion" id="formAccordion">
                    <div class="accordion-item">
                        <h4 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <b>INSTRUCCIONES - INFORMACIÓN</b>
                            </button>
                        </h4>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#formAccordion">
                            <div class="accordion-body">
                                <ol>
                                    <li class="pt-2">
                                       Los campos marcados con un <b class="text-danger">*</b> son obligatorios.
                                    </li>
                                    <li class="pt-2">
                                       Al jefe inmediato, se le pide avisar al/los empleados con periodos vacacionales rechazados y posteriormente eliminar estos.
                                    </li>
                                    <li class="pt-2">
                                        Los campos "grises" NO pueden ser modificados.
                                    </li>
                                    <li class="pt-2">
                                        No podrá registrar más dias de los que tiene asignados, es decir NO puede quedar a deber días.
                                    </li>
                                    <li class="pt-2">
                                        En el momento en que se quede sin dias restantes, es decir 0 el botón para registrar NO se mostrará.
                                    </li>
                                    <li class="pt-2">
                                        El periodo vacacional le será enviado vía correo electrónico a su jefe inmediato para que este las autorice.
                                    </li>
                                    <li class="pt-2">
                                        El máximo de días que puede escoger por periodo vacacional es 12 días.
                                    </li>
                                    <li class="pt-2">
                                        Se le pide NO registrar ningún periodo vacacional hasta que lo haya hablado/pactado con su jefe inmediato.
                                    </li>
                                    <li class="pt-2">
                                        Tanto al empleado como al jefe directo se le pide estar atentos de esta sección.
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            ');
        });

        $form->column(12 / 1, function ($form) {
            $form->html('
                <div class="accordion" id="formAccordionInstructions">
                    <div class="accordion-item">
                        <h4 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsetWO" aria-expanded="true" aria-controls="collapsetWO">
                                <b>LLENADO DE FORMULARIO</b>
                            </button>
                        </h4>
                        <div id="collapsetWO" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#formAccordionInstructions">
                            <div class="accordion-body">
                                <ol>
                                    <li class="pt-2">
                                        La fecha de inicio de las vacaciones debe ser al menos 3 días después del día actual.
                                    </li>
                                    <li class="pt-2">
                                        El campo Fecha Final se refiere a que es el día en el que desea terminar su vacaciones deseadas.
                                    </li>
                                    <li class="pt-2">
                                        El campo Día NO laboral 1. Este campo se refiere al/los días que por contrato NO viene a trabajar.
                                    </li>
                                    <li class="pt-2">
                                        El campo Día NO laboral 2. Este campo se refiere al/los días que por contrato NO viene a trabajar.
                                    </li>
                                    <li class="pt-2">
                                        El campo Día Extraordinario 1 se refiere al/los días que tendrá que venir/interrumpir sus vacaciones por cuestiones INUSUALES, obviamente esto es previamente acordado con su jefe inmediato.
                                    </li>
                                    <li class="pt-2">
                                        El campo Día Extraordinario 2 campo se refiere al/los días que tendrá que venir/interrumpir sus vacaciones por cuestiones INUSUALES, obviamente esto es previamente acordado con su jefe inmediato.
                                    </li>
                                    <li class="pt-2">
                                        El campo Total de dias seleccionados se refiere al total de días seleccionados en su periodo vacacional. Este campo NO puede ser modificado, pues se calcula automáticamente.
                                    </li>
                                    <li class="pt-2">
                                        El campo Días restantes se refiere al total de días que le quedan libres de vacaciones. Este campo NO puede ser modificado, pues se calcula automáticamente.
                                    </li>
                                    <li class="pt-2">
                                        La información de periodos vacacionales los podrá tomar en el siguiente orden: 2021 - 2021 | 2022 - 2023 | 2023 - 2024
                                    </li>
                                    <li class="pt-2">

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
            $userArea = Auth::user()->area;
            $user = Auth::user();
            $form->text('employee', __('Empleado'))->readonly()->value($userName)->icon('');
            $form->select('area', 'Área')
                ->options([
                    $userArea => $userArea,
                ])
                ->value($userArea)

                ->readonly();
            if ($userArea === 'RH-Intendencia-Chóferes-Asistente-Administrativo') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Jordana Pérez')
                    ->readonly()
                    ->icon('');
            } elseif ($userArea === 'Auditoría') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Gabriela Lozanos')
                    ->readonly()
                    ->icon('');
            } elseif ($userArea === 'Logística') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Esteban Hernández')
                    ->readonly()
                    ->icon('');
            } elseif ($userArea === 'Atenciónalcliente') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Teresa Fuentes')
                    ->readonly()
                    ->icon('');
            } elseif ($userArea === 'Mantenimiento-TI') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Gilberto Silva')
                    ->readonly()
                    ->icon('');
            } elseif ($userArea === 'Operaciones-Gerencias') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Fernando González')
                    ->readonly()
                    ->icon('');
            } elseif ($userArea === 'Operaciones') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Cristian Rizo')
                    ->readonly()
                    ->icon('');
            } elseif ($userArea === 'Transferencias') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Ericka Hidalgo')
                    ->readonly()
                    ->icon('');
            } elseif ($userArea === 'Desarrollo') {
                $form->text('Jefe', 'Jefe Inmediato')
                    ->value('Raul')
                    ->readonly()
                    ->icon('');
            }

            //$form->text('totalDays', __('Días Disponibles'))->readonly()->value($user->totalDays)->help('<strong>Este campo NO se puede modificar. La información aquí se refiere a que es el total de días que tiene al año.</strong>')->icon('');
            $form->hidden('color')->default('#074b07');
            $form->select('status', 'Estatus')
                ->options([
                    '' => 'Seleccione un estatus',
                    'Por Aprobar' => 'Por Aprobar',
                    'Aprobadas' => 'Aprobadas',
                    'Rechazadas' => 'Rechazadas',
                ])->when('Rechazadas', function (Form $form) {
                $form->hidden('rejectionReason', 'Razón de Rechazo')->value(' ')->readonly()->help('Escriba el motivo de el porque se ha rechazado este periodo vacacional.');
            })->value('Por Aprobar')->readonly();

            $previousVacation = Vacacion::where('user_id', $user->id)->latest()->first();
            if ($previousVacation) {
                $remainingDays = $previousVacation->remainingDays;
            } else {
                $remainingDays = $user->totalDays;
            }
            $form->text('remainingDays', __('Días restantes'))
                ->value($remainingDays)
                ->readonly()
                ->icon('');

            $form->html('
            <div class="accordion" id="periodsAccordion">
                <div class="accordion-item">
                    <h4 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePeriods" aria-expanded="false" aria-controls="collapsePeriods">
                            <b>INFORMACIÓN DE PERIODOS VACACIONALES</b>
                        </button>
                    </h4>
                    <div id="collapsePeriods" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#periodsAccordion">
                        <div class="accordion-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-xl-12 col-xxl-12">
                                        <h5 class="text-center">Los días aquí presentados son los que tiene disponibles por cada periodo.</h5 class="text-center">
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-xl-6 col-xxl-4">
                                        <label for="periodOne">Periodo 2021-2022:</label>
                                        <input class="form-control" type="text" id="periodOne" name="periodOne" value="' . ($user->periodOne !== null ? $user->periodOne : 0) . '" readonly>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-xl-6 col-xxl-4">
                                        <label for="periodTwo">Periodo 2022-2023:</label>
                                        <input class="form-control" type="text" id="periodTwo" name="periodTwo" value="' . ($user->periodTwo !== null ? $user->periodTwo : 0) . '" readonly>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-4 col-xl-6 col-xxl-4">
                                        <label for="periodThree">Periodo 2023-2024:</label>
                                        <input class="form-control" type="text" id="periodThree" name="periodThree" value="' . ($user->periodThree !== null ? $user->periodThree : 0) . '" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        ');

        });

        $form->column(7 / 1, function ($form) {
            $form->date('initialDate', __('Fecha Inicial'))
                ->rules([
                    'required',
                    'date',
                    'after:' . now()->format('Y-m-d'),
                    function ($attribute, $value, $fail) {
                        if ($value <= now()->addDays(2)->format('Y-m-d')) {
                            $fail('La fecha de inicio de las vacaciones debe ser al menos 3 días después del día actual.');
                        }
                        $existingRecord = Vacacion::where('employee', request('employee'))
                            ->where('initialDate', $value)
                            ->where('finalDate', request('finalDate'))
                            ->first();

                        if ($existingRecord && $existingRecord->id !== request('id')) {
                            $fail('Ya existe un periodo vacacional exactamente igual. Por favor elija otras fechas.');
                        }

                    },
                ])->placeholder('Fecha Inicial')
                ->attribute('style', 'width: 100%');

            $form->date('finalDate', __('Fecha Final'))
                ->rules([
                    'required',
                    'date',
                    'after_or_equal:' . now()->format('Y-m-d'),
                    function ($attribute, $value, $fail) {
                        if ($value == now()->subDay()->format('Y-m-d')) {
                            $fail('No puedes seleccionar el día anterior.');
                        }
                        $existingRecord = Vacacion::where('employee', request('employee'))
                            ->where('initialDate', request('initialDate'))
                            ->where('finalDate', $value)
                            ->first();

                        if ($existingRecord && $existingRecord->id !== request('id')) {
                            $fail('Ya existe un periodo vacacional exactamente igual. Por favor elija otras fechas.');
                        }
                    },
                ])->placeholder('Fecha Final')
                ->attribute('style', 'width: 100%');

            $form->date('extraInitialDate', __('Día NO laboral 1'))
                ->placeholder('Día NO laboral 1')->attribute('style', 'width: 100%');
            $form->date('extraFinalDate', __('Día NO laboral 2'))
                ->placeholder('Día NO laboral 2')->attribute('style', 'width: 100%');

            $form->date('extraordinaryDay1', __('Día EXTRAORDINARIO 1'))
                ->placeholder('Día extraordinario 1. ')
                ->attribute('style', 'width: 100%');

            $form->date('extraordinaryDay2', __('Día EXTRAORDINARIO 2'))
                ->placeholder('Día extraordinario 2')
                ->attribute('style', 'width: 100%');

            $form->text('daysDifference', __('Total de dias seleccionados'))->placeholder('Días seleccionados ')->readonly()->icon('');

        });

        $form->confirm('¿Está seguro que desea registrar esta información？');

        $form->saving(function (Form $form) {
            // Verificar si el único campo que cambia es 'status'
            if ($form->isUpdate() && $form->isOnlyUpdatingField('status')) {
                // Obtener el valor actual y anterior del campo 'status'
                $oldStatus = $form->model()->getOriginal('status');
                $newStatus = $form->input('status');

                // Verificar si el estado anterior es diferente al nuevo estado
                if ($oldStatus !== $newStatus) {
                    // Si los estados son diferentes, y el nuevo estado es 'Aprobadas', enviar el correo electrónico 'AprobadasMail'
                    if ($newStatus === 'Aprobadas') {
                        // Obtener el ID del registro de vacaciones que se está actualizando
                        $vacationId = $form->model()->id;
                        // Suponiendo que tengas el ID del usuario almacenado en una variable llamada $userId
                        Mail::to('raulmunozsaucedo@hotmail.com')
                            ->send(new AprobadasMail($vacationId));
                    } else {

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
                                $destinatario = 'raulmunozsaucedo@hotmail.com';
                        }

                        Mail::to($destinatario)
                            ->send(new MailVacaciones(
                                $form->employee,
                                $form->area,
                                $form->Jefe,
                                $form->initialDate,
                                $form->finalDate,
                                $form->extraInitialDate,
                                $form->extraFinalDate,
                                $form->totalDays,
                                $form->daysDifference,
                                $form->remainingDays,
                            ));
                    }
                }
            }
        });

        $form->saving(function (Form $form) use ($remainingDays) {
            $userId = Auth::id();
            User::subtractVacationDays($userId, $form->input('daysDifference'));
        });

        /* The above PHP code snippet is a part of a form saving function. It checks the value of the input
        field 'remainingDays' from the form. If the value of 'remainingDays' is less than 0, it displays
        an error message using admin_toastr() function indicating that there are not enough remaining
        vacation days. It then returns back to the previous page with the input values retained. */
        $form->saving(function (Form $form) {
            $remainingDays = $form->input('remainingDays');
            if ($remainingDays < 0) {
                admin_toastr('No tienes suficientes días restantes de vacaciones.', 'error', ['gravity' => 'bottom', 'position' => 'center']);
                return back()->withInput();
            }
        });

        /* The above PHP code is a snippet that is part of a form saving process. It calculates the difference
        in days between two dates (initialDate and finalDate) provided in the form. If the difference is
        greater than or equal to 12 days, it displays an error message using admin_toastr and prevents the
        form from being saved by returning back with input data. */
        $form->saving(function (Form $form) {
            $initialDate = $form->input('initialDate');
            $finalDate = $form->input('finalDate');
            $daysDifference = strtotime($finalDate) - strtotime($initialDate);
            $daysDifference = round($daysDifference / (60 * 60 * 24));
            if ($daysDifference >= 12) {
                admin_toastr('La diferencia entre la fecha inicial y la fecha final no puede ser mayor a 12 días seguidos.', 'error', ['gravity' => 'bottom', 'position' => 'center']);
                return back()->withInput();
            }
        });

        /* The above PHP code is a snippet that is being used to handle the saving process of a form. It is
        checking for overlapping vacation periods for a specific user. Here is a breakdown of what the code
        is doing: */
        $form->saving(function (Form $form) {
            $userId = Auth::id();
            $currentRecord = $form->model();
            $existingRecords = Vacacion::where('user_id', $userId)->get();
            foreach ($existingRecords as $existingRecord) {
                if (($form->initialDate >= $existingRecord->initialDate && $form->initialDate <= $existingRecord->finalDate) ||
                    ($form->finalDate >= $existingRecord->initialDate && $form->finalDate <= $existingRecord->finalDate)) {
                    if (!$currentRecord || $existingRecord->id !== $currentRecord->id) {
                        admin_toastr('El período vacacional se traslapa con uno existente. Por favor, seleccione otras fechas', 'error', ['gravity' => 'bottom', 'position' => 'center']);
                        return back()->withInput();
                    }
                } elseif ($form->initialDate <= $existingRecord->initialDate && $form->finalDate >= $existingRecord->finalDate) {
                    if (!$currentRecord || $existingRecord->id !== $currentRecord->id) {
                        admin_toastr('El período vacacional se traslapa con uno existente. Por favor, seleccione otras fechas', 'error', ['gravity' => 'bottom', 'position' => 'center']);
                        return;
                    }
                }
            }
        });

        $form->footer(function ($footer) use ($remainingDays) {
            // disable reset btn
            $footer->disableReset();
            // disable `View` checkbox
            $footer->disableViewCheck();
            // disable `Continue editing` checkbox
            $footer->disableEditingCheck();
            // disable `Continue Creating` checkbox
            $footer->disableCreatingCheck();
            if ($remainingDays <= 0) {
                $footer->disableSubmit();
            }

        });

        Admin::script("
        function calcularDiferenciaDias() {
            var initialDate = new Date(document.getElementById('initialDate').value);
            var finalDate = new Date(document.getElementById('finalDate').value);
            var extraInitialDate = document.getElementById('extraInitialDate').value;
            var extraFinalDate = document.getElementById('extraFinalDate').value;
            var extraordinaryDay1 = document.getElementById('extraordinaryDay1').value;
            var extraordinaryDay2 = document.getElementById('extraordinaryDay2').value;

            var timeDifference = Math.abs(finalDate.getTime() - initialDate.getTime());
            var daysDifference = Math.ceil(timeDifference / (1000 * 3600 * 24)) + 1; // Sumar 1 para incluir el día final

            var extraDaysDifference = 0;
            if (extraInitialDate && extraFinalDate) {
                var extraInitialDateObj = new Date(extraInitialDate);
                var extraFinalDateObj = new Date(extraFinalDate);
                var extraTimeDifference = Math.abs(extraFinalDateObj.getTime() - extraInitialDateObj.getTime());
                extraDaysDifference = Math.ceil(extraTimeDifference / (1000 * 3600 * 24)) + 1;
            }

            var extraordinaryDays = 0;
            if (extraordinaryDay1 && extraordinaryDay2) {
                var extraordinaryDay1Obj = new Date(extraordinaryDay1);
                var extraordinaryDay2Obj = new Date(extraordinaryDay2);
                var extraordinaryTimeDifference = Math.abs(extraordinaryDay2Obj.getTime() - extraordinaryDay1Obj.getTime());
                extraordinaryDays = Math.ceil(extraordinaryTimeDifference / (1000 * 3600 * 24)) + 1;
            } else if (extraordinaryDay1 || extraordinaryDay2) {
                extraordinaryDays = 1; // Si se selecciona solo uno de los días extraordinarios
            }

            var totalDaysDifference = daysDifference - extraDaysDifference - extraordinaryDays;
            document.getElementById('daysDifference').value = totalDaysDifference;

            // Calcular remainingDays
            var previousRemainingDays = parseInt(" . $remainingDays . ");
            var remainingDays = previousRemainingDays - totalDaysDifference;
            document.getElementById('remainingDays').value = remainingDays;
        }

        document.getElementById('initialDate').addEventListener('change', calcularDiferenciaDias);
        document.getElementById('finalDate').addEventListener('change', calcularDiferenciaDias);
        document.getElementById('extraInitialDate').addEventListener('change', calcularDiferenciaDias);
        document.getElementById('extraFinalDate').addEventListener('change', calcularDiferenciaDias);
        document.getElementById('extraordinaryDay1').addEventListener('change', calcularDiferenciaDias);
        document.getElementById('extraordinaryDay2').addEventListener('change', calcularDiferenciaDias);

    ");

        return $form;
    }

    public function destroy($id)
    {
        // Encontrar y eliminar la solicitud de vacaciones
        $vacation = Vacacion::findOrFail($id); // Busca la solicitud de vacaciones con el ID proporcionado
        $daysDifference = $vacation->daysDifference; // Guarda la diferencia en días entre la fecha inicial y final de las vacaciones
        $userId = $vacation->user_id; // Obtiene el ID del usuario asociado con la solicitud de vacaciones
        $vacation->delete(); // Elimina la solicitud de vacaciones

        // Recuperar el usuario correspondiente
        $user = User::findOrFail($userId); // Encuentra al usuario asociado a la solicitud de vacaciones

        // Incrementar periodThree y ajustar periodTwo si es necesario
        $user->periodThree += $daysDifference; // Incrementa el periodo tres con la diferencia de días
        if ($user->periodThree > $user->originalPeriodThree) {
            // Si periodThree excede su valor original, ajusta periodTwo y restablece periodThree al valor original
            $user->periodTwo += ($user->periodThree - $user->originalPeriodThree);
            $user->periodThree = $user->originalPeriodThree;
        }

        // Incrementar periodTwo y ajustar periodOne si es necesario
        if ($user->periodTwo > $user->originalPeriodTwo) {
            // Si periodTwo excede su valor original, ajusta periodOne y restablece periodTwo al valor original
            $user->periodOne += ($user->periodTwo - $user->originalPeriodTwo);
            $user->periodTwo = $user->originalPeriodTwo;
        }

        // Ajustar periodOne si es necesario
        if ($user->periodOne > $user->originalPeriodOne) {
            // Si periodOne excede su valor original, restablece periodOne al valor original
            $user->periodOne = $user->originalPeriodOne;
        }

        // Guardar los cambios en la tabla admin_users
        $user->save(); // Guarda los cambios realizados en la base de datos
    }

}
