<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;


    public $tieu_de;
    public $view;
    public $data;

    public function __construct($tieu_de, $view, $data)
    {
        $this->tieu_de = $tieu_de;
        $this->view    = $view;
        $this->data    = $data;
    }

    public function build()
    {
        return $this->subject($this->tieu_de)->view($this->view, ['dataMail' => $this->data]);
    }
}
