<?php

namespace Modules\Keuangan\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransaksiKasBankExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithColumnFormatting
{
    protected string $jenis;
    protected $transactions;
    protected array $meta;

    public function __construct(string $jenis, $transactions, array $meta = [])
    {
        $this->jenis = $jenis;
        $this->transactions = $transactions;
        $this->meta = $meta;
    }

    public function collection()
    {
        $data = collect();
        $no = 1;
        $total = 0;

        foreach ($this->transactions as $item) {
            $total += (float) $item->nominal;

            $namaKasBank = ($item->kasBank ? $item->kasBank->kode_akun . ' - ' . $item->kasBank->nama_akun : '-');
            $namaLawan = ($item->lawanCoa ? $item->lawanCoa->kode_akun . ' - ' . $item->lawanCoa->nama_akun : '-');

            $data->push([
                'no'                => $no++,
                'nomor_transaksi'   => $item->nomor_transaksi,
                'tanggal'           => Carbon::parse($item->tanggal)->format('d/m/Y'),
                'kas_bank'          => $namaKasBank,
                'lawan_coa'         => $namaLawan,
                'penerima_pembayar' => $item->penerima_pembayar ?: '-',
                'metode_pembayaran' => $item->metode_pembayaran ?: '-',
                'nomor_referensi'   => $item->nomor_referensi ?: '-',
                'keterangan'        => $item->keterangan ?: '-',
                'nominal'           => (float) $item->nominal,
            ]);
        }

        // Add Total row
        $data->push([
            'no'                => '',
            'nomor_transaksi'   => '',
            'tanggal'           => '',
            'kas_bank'          => '',
            'lawan_coa'         => '',
            'penerima_pembayar' => '',
            'metode_pembayaran' => '',
            'nomor_referensi'   => '',
            'keterangan'        => 'TOTAL REKAPITULASI',
            'nominal'           => $total,
        ]);

        return $data;
    }

    public function headings(): array
    {
        $isMasuk = ($this->jenis === 'masuk');
        $title = $isMasuk
            ? 'LAPORAN REKAPITULASI TRANSAKSI KAS & BANK MASUK'
            : 'LAPORAN REKAPITULASI TRANSAKSI KAS & BANK KELUAR';

        $periode = $this->meta['periode'] ?? 'Semua Periode';
        $kasBankFilter = $this->meta['kas_bank'] ?? 'Semua Akun Kas/Bank';
        $lawanFilter = $this->meta['lawan_coa'] ?? ($isMasuk ? 'Semua Akun Lawan' : 'Semua Akun Beban');
        $printedAt = 'Dicetak pada: ' . Carbon::now()->translatedFormat('d F Y H:i') . ' WIB';

        $kasBankHeader = $isMasuk ? 'Akun Kas / Bank (Debit)' : 'Akun Kas / Bank (Kredit)';
        $lawanHeader = $isMasuk ? 'Akun Lawan (Kredit)' : 'Akun Beban / Lawan (Debit)';
        $pihakHeader = $isMasuk ? 'Diterima Dari' : 'Dibayarkan Kepada';

        return [
            [$title],
            ['Periode:', $periode],
            ['Filter Kas/Bank:', $kasBankFilter],
            ['Filter Lawan CoA:', $lawanFilter],
            [$printedAt],
            [''],
            [
                'No',
                'No. Transaksi',
                'Tanggal',
                $kasBankHeader,
                $lawanHeader,
                $pihakHeader,
                'Metode Pembayaran',
                'No. Referensi',
                'Keterangan',
                'Nominal (Rp)',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $isMasuk = ($this->jenis === 'masuk');

        $headerColor = $isMasuk ? 'E6F4EA' : 'FCE8E6';
        $totalColor = 'F1F5F9';

        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle('A2:A5')->getFont()->setBold(true);
        $sheet->getStyle('A7:J7')->getFont()->setBold(true);
        $sheet->getStyle('A7:J7')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($headerColor);

        // Borders for table data (from row 7 to last row)
        $sheet->getStyle('A7:J' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Total row style
        $sheet->getStyle('A' . $lastRow . ':J' . $lastRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $lastRow . ':J' . $lastRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($totalColor);
        $sheet->getStyle('I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Alignment
        $sheet->getStyle('A8:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B8:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C8:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'J' => '#,##0',
        ];
    }

    public function title(): string
    {
        return ($this->jenis === 'masuk') ? 'Kas Bank Masuk' : 'Kas Bank Keluar';
    }
}
