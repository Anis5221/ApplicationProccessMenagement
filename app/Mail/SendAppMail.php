<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAppMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $details;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }


    public function build()
    {
        return $this->from($address = 'stroberi.ttmt@gmail.com', $name = 'NUB')
                    ->subject('Hi, Your application Is Accepted!')
        ->view('contents.application-check.show')->with(['application' =>$this->details]);
    }
}