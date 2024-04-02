<?php

namespace App\Imports;

use App\Models\EmpleadoRH;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class EmpleadoRHImport implements ToCollection, WithHeadingRow, WithStartRow
{

    public function startRow(): int
    {
        return 3; // Omitir la primera fila (encabezado)
    }

    public function collection(Collection $rows)
    {

        foreach ($rows as $row) {

            $entryDate = Carbon::createFromFormat('d/m/Y', '01/01/1900')
                ->addDays(intval($row['4']) - 2)
                ->toDateString();

            $birthdayDate = Carbon::createFromFormat('d/m/Y', '01/01/1900')
                ->addDays(intval($row['13']) - 2)
                ->toDateString();

            EmpleadoRH::create([
                'NumeroEmpleado' => $row['0'],
                'Departamento' => $row['1'],
                'Posicion' => $row['2'],
                'Empleado' => $row['3'],
                'FechaEntrada' => Carbon::createFromFormat('d/m/Y', '01/01/1900')
                    ->addDays(intval($row['4']) - 2)
                    ->toDateString(),
                'Direccion' => $row['5'],
                'RFC' => $row['6'],
                'Curp' => $row['7'],
                'NSS' => $row['8'],
                'Banco' => $row['9'],
                'CuentaBancaria' => $row['10'],
                'ClabeBanco' => $row['11'],
                'Correo' => $row['12'],
                'FechaNacimiento' => Carbon::createFromFormat('d/m/Y', '01/01/1900')
                    ->addDays(intval($row['13']) - 2)
                    ->toDateString(),
                'Sede' => $row['14'],
                'Estatus' => $row['15'],
            ]);
        }
    }
}
