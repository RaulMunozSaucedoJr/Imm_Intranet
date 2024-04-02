<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailVacaciones extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $area;
    public $jefe;
    public $initialDate;
    public $finalDate;
    public $extraInitialDate;
    public $extraFinalDate;
    public $totalDays;
    public $daysDifference;
    public $remainingDays;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($employee, $area,
        $jefe, $initialDate, $finalDate, $extraInitialDate,
        $extraFinalDate, $totalDays, $daysDifference, $remainingDays) {
        $this->employee = $employee;
        $this->area = $area;
        $this->jefe = $jefe;
        $this->initialDate = $initialDate;
        $this->finalDate = $finalDate;
        $this->extraInitialDate = $extraInitialDate;
        $this->extraFinalDate = $extraFinalDate;
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
                'area' => $this->area,
                'jefe' => $this->jefe,
                'initialDate' => $this->initialDate,
                'finalDate' => $this->finalDate,
                'extraInitialDate' => $this->extraInitialDate,
                'extraFinalDate' => $this->extraFinalDate,
                'totalDays' => $this->totalDays,
                'daysDifference' => $this->daysDifference,
                'remainingDays' => $this->remainingDays,
            ]);
    }
}
