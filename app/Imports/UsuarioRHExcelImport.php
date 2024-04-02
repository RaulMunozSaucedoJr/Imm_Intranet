<?php

namespace App\Imports;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

//use Maatwebsite\Excel\Concerns\WithHeadingRow;
//use Maatwebsite\Excel\Concerns\WithStartRow;
//

class UsuarioRHExcelImport implements ToCollection
{

    /*
    public function startRow(): int
    {
    return 3; // Omitir la primera fila (encabezado)
    }
     */

    public function collection(Collection $rows)
    {

        foreach ($rows as $row) {

            /*
            $entryDate = Carbon::createFromFormat('d/m/Y', '01/01/1900')
            ->addDays(intval($row['4']) - 2)
            ->toDateString();
             */

            User::create([
                'employeeNumber' => $row['0'],
                'username' => $row['1'],
                'name' => $row['2'],
                'entryDate' => Carbon::createFromFormat('d/m/Y', '01/01/1900')
                    ->addDays(intval($row['3']) - 2)
                    ->toDateString(),
                'periodOne' => $row['4'],
                'periodTwo' => $row['5'],
                'periodThree' => $row['6'],
                'originalPeriodOne' => $row['7'],
                'originalPeriodTwo' => $row['8'],
                'originalPeriodThree' => $row['9'],
                'totalDays' => $row['10'],
                'area' => $row['11'],
                'boss' => $row['12'],
                'headquarter' => $row['13'],

            ]);
        }
    }
}
