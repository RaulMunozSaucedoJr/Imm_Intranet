<?php

namespace App\Admin\Actions\Post;

use Illuminate\Database\Eloquent\Model;
use OpenAdmin\Admin\Actions\RowAction;

class Restore extends RowAction
{
    public $name = 'Restore';
    public $icon = 'icon-trash-restore';

    public function handle(Model $model)
    {
        $model->restore();

        return $this->response()->success('Recuperado')->refresh();
    }

    public function dialog()
    {
        $this->confirm('¿Está seguro de que desea restaurar este registro?');
    }
}
