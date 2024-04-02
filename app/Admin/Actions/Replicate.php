<?php

namespace App\Admin\Actions;

use Illuminate\Database\Eloquent\Model;
use OpenAdmin\Admin\Actions\RowAction;

class Replicate extends RowAction
{
    public $name = 'Clonar';

    public $icon = 'icon-copy';

    public function handle(Model $model)
    {
        $model->replicate()->save();
        return $this->response()->success('Registro Copiado')->refresh();
    }

    public function dialog()
    {
        $this->question('¿Esta seguro que desea crear una copia de este registro?', 'Esto creará otro registro con exactamente la misma información, por lo que se tendrá información duplicada', ['icon' => 'question', 'confirmButtonText' => 'Si', 'cancelButtonText' => 'Cancelar']);
    }
}
