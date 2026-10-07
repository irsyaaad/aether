<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nama,$data,$email)
    {
        $this->nama = $nama;
        $this->data = $data;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('irsyad7798@gmail.com')
            ->subject('Berikut Informasi dari Barang Anda')
            ->cc($this->email)
            ->view('email')
            ->with(
            [
                'nama' => $this->nama,
                'data' => $this->data
            ]);
    }
}
