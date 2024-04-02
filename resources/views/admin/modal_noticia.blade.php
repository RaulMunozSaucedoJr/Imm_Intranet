<!-- Modal -->
<div class="modal fade" id="exampleModal{{ $noticia->id }}" tabindex="-1" aria-labelledby="exampleModalLabel{{ $noticia->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel{{ $noticia->id }}">Detalles de la Noticia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>{{ $noticia->Titulo }}</h5>
                <p>{{ $noticia->Descripcion }}</p>
                <p>Tema: {{ $noticia->Tema }}</p>
                <p>Fecha: {{ $noticia->Fecha }}</p>
                <p>Creador: {{ $noticia->usuario }}</p>
            </div>
        </div>
    </div>
</div>
