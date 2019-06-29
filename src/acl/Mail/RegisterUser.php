<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterUser extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var $hash
     */
    protected $hash;

    /**
     * RegisterUser constructor.
     * @param $hash
     */
    public function __construct($hash)
    {
        $this->hash = $hash;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.registerUser', ['hash' => $this->hash]);
    }
}
