<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RejectMail extends Mailable
{
    use Queueable, SerializesModels;
    public $nama, $url, $logo, $nomor, $tanggal, $keterangan;

    /**
     * Create a new message instance.
     */
    public function __construct($nama, $url, $nomor, $tanggal, $keterangan)
    {
        $this->nama = $nama;
        $this->url = $url;
        $this->logo = asset('cuba/images/logo/logo.png'); // ganti path sesuai file logo
        $this->nomor = $nomor;
        $this->tanggal = $tanggal;
        $this->keterangan = $keterangan;
    }

    public function build()
    {
        $email = $this->subject('📄 Surat Keterangan Anda Telah Terbit')
            ->view('emails.reject_surat_keterangan')
            ->with([
                'nama' => $this->nama,
                'url' => $this->url,
                'logo' => $this->logo,
                'nomor' => $this->nomor,
                'tanggal' => $this->tanggal,
                'keterangan' => $this->keterangan,
            ]);

        return $email;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sistem Informasi Karyawan - PT Putera Adhi Perkasa',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reject_surat_keterangan',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
