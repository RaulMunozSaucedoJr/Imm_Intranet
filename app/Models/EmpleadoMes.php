<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmpleadoMes extends Model
{
    use SoftDeletes;

    protected $table = 'empleado-mes';
}
