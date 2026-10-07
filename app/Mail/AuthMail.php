<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AuthMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user = [];
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {   
        //dd($this->user);
        return $this->view('mailborongan')->with([
            "kode"  => $this->user["kode"],
            "user"  => $this->user["name"]
        ])->from('cfcyoga@gmail.com', 'lsj-express')->subject("Auhtentikasi Kode Borongan");
    }
}
