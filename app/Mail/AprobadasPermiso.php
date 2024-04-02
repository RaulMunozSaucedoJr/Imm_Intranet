<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AprobadasPermiso extends Mailable
{
    use Queueable, SerializesModels;

    public $Estatus;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Estatus)
    {
        $this->Estatus = $Estatus;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.aprobadasPermiso')
            ->subject('Permiso Aprobado')
            ->with([
                'Estatus' => $this->Estatus,
            ]);
    }
}
