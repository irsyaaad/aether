<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuratKeteranganMail extends Mailable
{
    use Queueable, SerializesModels;
    public $nama, $url, $logo, $nomor, $tanggal, $attachment;

    /**
     * Create a new message instance.
     */
    public function __construct($nama, $url, $nomor, $tanggal, $attachment = null)
    {
        $this->nama = $nama;
        $this->url = $url;
        $this->logo = asset('cuba/images/logo/logo.png'); // ganti path sesuai file logo
        $this->nomor = $nomor;
        $this->tanggal = $tanggal;
        $this->attachment = $attachment;
    }

    public function build()
    {
        $email = $this->subject('📄 Surat Keterangan Anda Telah Terbit')
            ->view('emails.surat_keterangan')
            ->with([
                'nama' => $this->nama,
                'url' => $this->url,
                'logo' => $this->logo,
                'nomor' => $this->nomor,
                'tanggal' => $this->tanggal,
            ]);

        // Jika ada lampiran PDF
        if ($this->attachment && file_exists($this->attachment)) {
            $email->attach($this->attachment, [
                'as' => 'Surat-Keterangan.pdf',
                'mime' => 'application/pdf',
            ]);
        }

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
            markdown: 'emails.surat_keterangan',
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
