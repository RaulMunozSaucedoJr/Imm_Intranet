<?php

namespace App\Http\Livewire;

use App\Models\Noticia;
use Livewire\Component;

class NoticiasComponent extends Component
{
    public $noticias;

    public function mount()
    {
        $this->noticias = Noticia::paginate(10); // Puedes ajustar el número de noticias por página aquí
    }

    public function loadMore()
    {
        $this->noticias = $this->noticias->concat(Noticia::paginate(10));
    }

    public function render()
    {
        return view('livewire.noticias-component');
    }
}
