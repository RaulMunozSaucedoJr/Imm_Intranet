<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailTickets extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $Departamento;
    public $jefe;
    public $initialDate;
    public $finalDate;
    public $extraInitialDate;
    public $extraFinalDate;
    public $motive;
    public $totalDays;
    public $daysDifference;
    public $remainingDays;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($employee, $Departamento,
        $jefe, $initialDate, $finalDate, $extraInitialDate,
        $extraFinalDate, $motive, $totalDays, $daysDifference, $remainingDays) {
        $this->employee = $employee;
        $this->Departamento = $Departamento;
        $this->jefe = $jefe;
        $this->initialDate = $initialDate;
        $this->finalDate = $finalDate;
        $this->extraInitialDate = $extraInitialDate;
        $this->extraFinalDate = $extraFinalDate;
        $this->motive = $motive;
        $this->totalDays = $totalDays;
        $this->daysDifference = $daysDifference;
        $this->remainingDays = $remainingDays;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.vacaciones')
            ->subject('Solicitud de Vacaciones')
            ->with([
                'employee' => $this->employee,
                'Departamento' => $this->Departamento,
                'jefe' => $this->jefe,
                'initialDate' => $this->initialDate,
                'finalDate' => $this->finalDate,
                'extraInitialDate' => $this->extraInitialDate,
                'extraFinalDate' => $this->extraFinalDate,
                'motive' => $this->motive,
                'totalDays' => $this->totalDays,
                'daysDifference' => $this->daysDifference,
                'remainingDays' => $this->remainingDays,
            ]);
    }
}
