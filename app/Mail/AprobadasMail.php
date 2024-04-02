<?php

namespace App\Mail;

use App\Models\Vacacion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AprobadasMail extends Mailable
{
    use Queueable, SerializesModels;

    public $status;
    public $vacation;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($vacationId)
    {
        // Obtener los detalles de la vacación específica
        $this->vacation = Vacacion::findOrFail($vacationId);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.aprobadas')
            ->subject('Vacaciones Aprobadas')
            ->with([
                'status' => $this->vacation->status,
                'lastVacation' => $this->vacation,
            ]);
    }
}
