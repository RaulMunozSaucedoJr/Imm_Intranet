<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailReservaciones extends Mailable
{
    use Queueable, SerializesModels;

    public $Empleado;
    public $Sede;
    public $Sala;
    public $Descripcion;
    public $Fecha;
    public $HoraInicial;
    public $HoraFinal;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Empleado, $Sede,
        $Sala, $Descripcion, $Fecha, $HoraInicial,
        $HoraFinal) {
        $this->Empleado = $Empleado;
        $this->Sede = $Sede;
        $this->Sala = $Sala;
        $this->Descripcion = $Descripcion;
        $this->Fecha = $Fecha;
        $this->HoraInicial = $HoraInicial;
        $this->HoraFinal = $HoraFinal;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.reservaciones')
            ->subject('Reservación de Sala')
            ->with([
                'Empleado' => $this->Empleado,
                'Sede' => $this->Sede,
                'Sala' => $this->Sala,
                'Descripcion' => $this->Descripcion,
                'Fecha' => $this->Fecha,
                'HoraInicial' => $this->HoraInicial,
                'HoraFinal' => $this->HoraFinal,
            ]);
    }
}
