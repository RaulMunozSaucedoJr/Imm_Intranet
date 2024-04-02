<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class EmpleadoRHExcel extends Model
{
    use SoftDeletes;

    protected $table = 'empleadosRH';

    protected $fillable = [
        'NumeroEmpleado',
        'Departamento',
        'Posicion',
        'Empleado',
        'FechaEntrada',
        'Direccion',
        'RFC',
        'Curp',
        'NSS',
        'Banco',
        'CuentaBancaria',
        'ClabeBanco',
        'Correo',
        'FechaNacimiento',
        'Sede',
        'Estatus',
    ];

    public $sortable = [
        'order_column_name' => 'order_column',
        'sort_when_creating' => true,
    ];
}
