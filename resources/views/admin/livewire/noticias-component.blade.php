<div>
    @foreach ($noticias as $noticia)
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $noticia->Titulo }}</h5>
                <p class="card-text">{{ $noticia->Descripcion }}</p>
                <p class="card-text">Tema: {{ $noticia->Tema }}</p>
                <p class="card-text">Fecha: {{ $noticia->Fecha }}</p>
                <p class="card-text">Registró: {{ $noticia->usuario }}</p>
            </div>
        </div>
    @endforeach

    <button wire:click="loadMore">Cargar más</button>
</div>
