<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comunicado;
use App\Models\EmpleadoMes;
use App\Models\Evento;
use App\Models\Noticia;
use Illuminate\Http\Request;
use OpenAdmin\Admin\Admin;
use OpenAdmin\Admin\Layout\Column;
use OpenAdmin\Admin\Layout\Content;
use OpenAdmin\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content, Request $request)
    {
        Admin::style('
        .btn-primary {
            background-color: #2b2c50;
            color:#fffff;
            font-weight:bold;
            border-radius: .5rem;
        }

        .btn-danger{
            border-radius: .5rem;
        }

        .modal-content{
            border-radius: .5rem;
        }

        .btn-primary:hover {
            background-color: #2b2c50; /* Mismo color de fondo en hover */
        }

        .form-control {
            border-radius: 1rem;
        }

        .card {
            border-radius: 1rem;
            height: 250px;
        }

        .card-body {
            width: 100%;
            height: 100%;
            border-radius: 1rem;
        }

        .card-title {
            font-weight: bolder;
            text-align: left;
        }
        .card p {
            color: #1c1c1c;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: left;
            font-weight: light;
            line-height: 2em;
        }

        .pagination {
            width: 90%;
            height: 100%;
            background-color: #2b2c50;
            padding: 1rem;
            border-radius: 1rem;
            object-fit: contain;
        }

        .section-divider {
            border: none;
            height: 2px;
            background-color: #1c1c1c;
            margin: 1em;
        }

        .pagination-links {
            position: fixed;
            bottom: 0;
            width: 70%;
            object-fit: contain;
            z-index: 1;
        }

        .btn-back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 999;
            background-color: #2b2c50;
            color: #fff;
            border: none;
            border-radius: 20%;
            width: 50px;
            height: 50px;
            font-size: 20px;
            line-height: 30px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: none;
        }

        .btn-back-to-top:hover {
            background-color: #2b2c50;
        }
    ');

        // Obtén el término de búsqueda del formulario
        $searchTitle = $request->input('Titulo');
        $searchDate = $request->input('search_date');

        // Obtiene las noticias que coinciden con el término de búsqueda
        $noticias = Noticia::where('Titulo', 'like', "%$searchTitle%")
            ->where('Fecha', 'like', "%$searchDate%")
            ->orderBy('Fecha', 'desc')
            ->paginate(3);

        // Obtiene los eventos
        $eventos = Evento::where('Titulo', 'like', "%$searchTitle%")
            ->where('Fecha', 'like', "%$searchDate%")
            ->orderBy('Fecha', 'desc')
            ->paginate(3);

        // Obtiene los comunicados
        $comunicados = Comunicado::where('Titulo', 'like', "%$searchTitle%")
            ->where('Fecha', 'like', "%$searchDate%")
            ->orderBy('Fecha', 'desc')
            ->paginate(3);

        // Obtiene la información de empleado-mes
        $empleadoMes = EmpleadoMes::all();

        return $content->row(function (Row $row) use ($noticias, $searchTitle, $searchDate, $eventos, $comunicados, $empleadoMes) {

            // Agregar el formulario de búsqueda para noticias
            $row->column(5, function (Column $column) use ($searchTitle) {
                $column->row('<form action="" method="GET" style="margin-bottom: 20px;">
                                <div class="input-group">
                                    <input type="text" name="Titulo" id="Titulo" class="form-control" placeholder="Buscar por título..." value="' . $searchTitle . '">
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn" style="background-color: #2b2c50; color:white;">Buscar</button>
                                    </span>
                                </div>
                            </form>');
            });

            // Agregar el formulario de búsqueda para eventos
            $row->column(5, function (Column $column) use ($searchDate) {
                $column->row('<form action="" method="GET" style="margin-bottom: 20px;">
                                <div class="input-group">
                                    <input type="date" name="search_date" class="form-control" placeholder="Buscar por fecha..." value="' . $searchDate . '">
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn" style="background-color: #2b2c50; color:white;">Buscar</button>
                                    </span>
                                </div>
                            </form>');
            });

            $row->column(2, function (Column $column) {
                $column->row('<form action="" method="GET" style="margin-bottom: 20px;">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <a href="http://192.168.100.26:8000/admin" class="btn btn-md btn-warning">
                                            <span class="text-black">
                                                <strong>Limpiar Búsqueda</strong>
                                            </span>
                                        </a>
                                    </span>
                                </div>
                            </form>');
            });

            // Mostrar las noticias
            $row->column(12, function (Column $column) use ($noticias) {
                $column->row('
                <div class="container-fluid mt-4 mb-4">
                    <div class="row">
                        <div class="col-4">
                            <h1>Noticias</h1>
                        </div>
                        <div class="col-8">
                            <hr class="section-divider">
                        </div>
                    </div>
                </div>
            ');

                $column->row(function (Row $row) use ($noticias) {
                    foreach ($noticias as $noticia) {
                        $row->column(3, '
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">' . $noticia->Titulo . '</h5>
                                <p class="card-text">Fecha: ' . $noticia->Fecha . '</p>
                                <p class="card-text">' . $noticia->Descripcion . '</p>
                                <p class="card-text">Creador: ' . $noticia->usuario . '</p>

                            </div>
                            <div class="card-footer">
                                <button type="button" class="btn btn-primary read-more-btn w-100" data-bs-toggle="modal" data-bs-target="#modalNoticias' . $noticia->id . '">
                                    Leer más
                                </button>
                            </div>
                        </div>
                        ');

                        // Modal dinámico para cada noticia
                        $row->column(1, '
                            <div class="modal fade" id="modalNoticias' . $noticia->id . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h3 class="modal-title">' . $noticia->Titulo . '</h3>
                                        </div>
                                        <div class="modal-body">
                                            <p class="card-text">Fecha: ' . $noticia->Fecha . '</p>
                                            <p class="card-text">' . $noticia->Descripcion . '</p>
                                            <p class="card-text">Creador: ' . $noticia->usuario . '</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ');
                    }
                });
            });

            // Mostrar los eventos
            $row->column(12, function (Column $column) use ($eventos) {
                $column->row('
                    <div class="container-fluid mt-4 mb-4">
                        <div class="row">
                            <div class="col-4">
                                <h1>Eventos</h1>
                            </div>
                            <div class="col-8">
                                <hr class="section-divider">
                            </div>
                        </div>
                    </div>
                ');

                $column->row(function (Row $row) use ($eventos) {
                    foreach ($eventos as $evento) {
                        $row->column(3, '
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">' . $evento->Titulo . '</h5>
                                <p class="card-text">Fecha: ' . $evento->Fecha . '</p>
                                <p class="card-text">' . $evento->Descripcion . '</p>
                                <p class="card-text">Creador: ' . $evento->usuario . '</p>

                            </div>
                            <div class="card-footer">
                                <button type="button" class="btn btn-primary read-more-btn w-100" data-bs-toggle="modal" data-bs-target="#modalEventos' . $evento->id . '">
                                    Leer más
                                </button>
                            </div>
                        </div>
                        ');

                        $row->column(1, '
                        <div class="modal fade" id="modalEventos' . $evento->id . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h3 class="modal-title">' . $evento->Titulo . '</h3>
                                    </div>
                                    <div class="modal-body">
                                        <p class="card-text">Fecha: ' . $evento->Fecha . '</p>
                                        <p class="card-text">' . $evento->Descripcion . '</p>
                                        <p class="card-text">Creador: ' . $evento->usuario . '</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ');
                    }
                });

                $column->row('<div class="pagination-links">' . $eventos->links() . '</div>');
            });

            // Mostrar los comunicados
            $row->column(12, function (Column $column) use ($comunicados) {
                $column->row('
                    <div class="container-fluid mt-4 mb-4">
                        <div class="row">
                            <div class="col-4">
                                <h1>Comunicados</h1>
                            </div>
                            <div class="col-8">
                                <hr class="section-divider">
                            </div>
                        </div>
                    </div>
                ');

                $column->row(function (Row $row) use ($comunicados) {
                    foreach ($comunicados as $comunicado) {
                        $row->column(3, '
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">' . $comunicado->Titulo . '</h5>
                                    <p class="card-text">Fecha: ' . $comunicado->Fecha . '</p>
                                    <p class="card-text">' . $comunicado->Descripcion . '</p>
                                    <p class="card-text">Creador: ' . $comunicado->usuario . '</p>

                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-primary read-more-btn w-100" data-bs-toggle="modal" data-bs-target="#modalComunicados' . $comunicado->id . '">
                                        Leer más
                                    </button>
                                </div>
                            </div>
                        ');

                        $row->column(1, '
                        <div class="modal fade" id="modalComunicados' . $comunicado->id . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h3 class="modal-title">' . $comunicado->Titulo . '</h3>
                                    </div>
                                    <div class="modal-body">
                                        <p class="card-text">Fecha: ' . $comunicado->Fecha . '</p>
                                        <p class="card-text">' . $comunicado->Descripcion . '</p>
                                        <p class="card-text">Creador: ' . $comunicado->usuario . '</p>
                                        <p>' . $comunicado->Descripcion . '</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger w-100" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ');
                    }
                });

                $column->row('<div class="pagination-links">' . $comunicados->links() . '</div>');
            });

            // Mostrar la información de empleado-mes
            $row->column(12, function (Column $column) use ($empleadoMes) {
                $column->row('
                    <div class="container-fluid mt-4 mb-4">
                        <div class="row">
                            <div class="col-4">
                                <h1>Empleado del Mes</h1>
                            </div>
                            <div class="col-8">
                                <hr class="section-divider">
                            </div>
                        </div>
                    </div>
                ');

                $column->row(function (Row $row) use ($empleadoMes) {
                    foreach ($empleadoMes as $registro) {
                        $row->column(12, '
                            <div class="card">
                                <div class="card-body">
                                    <h2 class="card-title">' . $registro->Empleado . '</h2>
                                    <p class="card-text">' . $registro->Descripcion . '</p>
                                    <p class="card-text">Fecha: ' . $registro->Fecha . '</p>
                                    <p class="card-text">Creador: ' . $registro->usuario . '</p>
                                </div>
                            </div>
                        ');
                    }
                });
            });

            Admin::script('
                document.addEventListener("scroll", function() {
                    var btnBackToTop = document.querySelector(".btn-back-to-top");
                    if (window.scrollY > 100) {
                        btnBackToTop.style.display = "block";
                    } else {
                        btnBackToTop.style.display = "none";
                    }
                });
                document.addEventListener("DOMContentLoaded", function() {
                    var btnBackToTop = document.createElement("button");
                    btnBackToTop.innerHTML = "&#129033;";
                    btnBackToTop.classList.add("btn-back-to-top");
                    btnBackToTop.addEventListener("click", function() {
                        window.scrollTo({
                            top: 0,
                            behavior: "smooth"
                        });
                    });
                    document.body.appendChild(btnBackToTop);
                });
            ');
        });
    }
}
