<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpleadoRH extends Model
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
}
