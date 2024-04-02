<?php

namespace App\Admin\Actions\Post;

use OpenAdmin\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class BatchRestore extends BatchAction
{
    public $name = 'Recuperar';

    public function handle (Collection $collection)
    {
        $collection->each->restore();

        return $this->response()->success('Recuperada')->refresh();
    }

    public function dialog ()
    {
        $this->confirm('¿Está seguro que desea recuperar esta información?.');
    }
}