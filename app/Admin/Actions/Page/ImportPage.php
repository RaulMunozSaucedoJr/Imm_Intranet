<?php

namespace App\Admin\Actions\Page;

use OpenAdmin\Admin\Actions\Action;
use Illuminate\Http\Request;

class ImportPage extends Action
{
    public $name = 'Importar Excel Colaboradores';

    protected $selector = '.import-page';

    public function handle(Request $request)
    {
        // The following code gets the uploaded file, then uses the package `maatwebsite/excel` to process and upload your file and save it to the database.
        $path = $request->file('file');

        return $this->response()->success('Importación Completa'.$path)->refresh();
    }

    public function form()
    {
        $this->file('file', 'Seleccione un archivo a subir');
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-light import-page"><i class="icon-upload"></i>Importar Excel</a>
HTML;
    }
}