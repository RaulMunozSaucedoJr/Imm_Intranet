<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailPermiso extends Mailable
{
    use Queueable, SerializesModels;

    public $Empleado;
    public $Fecha;
    public $Departamento;
    public $Jefe;
    public $Motivo;
    public $Descripcion;
    public $Estatus;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Empleado, $Fecha, $Departamento, $Jefe, $Motivo, $Descripcion, $Estatus)
    {
        $this->Empleado = $Empleado;
        $this->Fecha = $Fecha;
        $this->Departamento = $Departamento;
        $this->Jefe = $Jefe;
        $this->Motivo = $Motivo;
        $this->Descripcion = $Descripcion;
        $this->Estatus = $Estatus;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.permiso')
            ->subject('Solicitud de Permiso')
            ->with([
                'Empleado' => $this->Empleado,
                'Fecha' => $this->Fecha,
                'Departamento' => $this->Departamento,
                'Jefe' => $this->Jefe,
                'Motivo' => $this->Motivo,
                'Descripcion' => $this->Descripcion,
                'Estatus' => $this->Estatus,
            ]);
    }
}
