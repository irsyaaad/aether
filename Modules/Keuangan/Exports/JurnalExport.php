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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JurnalExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithColumnFormatting
{
    protected array $entries;
    protected array $meta;
    protected object $summary;

    public function __construct(array $entries, array $meta = [], ?object $summary = null)
    {
        $this->entries = $entries;
        $this->meta = $meta;
        $this->summary = $summary ?? (object) [
            'total_debit'  => 0,
            'total_kredit' => 0,
            'is_balanced'  => true,
            'status_label' => 'Seimbang (Balance)',
        ];
    }

    public function collection()
    {
        $data = collect();
        $no = 1;
        $lastTxId = null;

        foreach ($this->entries as $row) {
            $isKredit = ($row->posisi === 'Kredit');
            $namaAkunFormatted = $isKredit ? '    ' . $row->nama_akun : $row->nama_akun;

            // Nomor baris hanya ditampilkan pada baris pertama setiap transaksi
            $nomorUrut = ($row->transaksi_id !== $lastTxId) ? $no++ : '';
            $nomorTransaksi = ($row->transaksi_id !== $lastTxId) ? $row->nomor_transaksi : '';
            $tanggal = ($row->transaksi_id !== $lastTxId) ? Carbon::parse($row->tanggal)->format('d/m/Y') : '';
            $keterangan = ($row->transaksi_id !== $lastTxId) ? ($row->keterangan ?: '-') : '';

            $data->push([
                'no'              => $nomorUrut,
                'tanggal'         => $tanggal,
                'nomor_transaksi' => $nomorTransaksi,
                'keterangan'      => $keterangan,
                'pihak'           => $row->penerima_pembayar ?: '-',
                'kode_akun'       => $row->kode_akun ?: '-',
                'nama_akun'       => $namaAkunFormatted,
                'posisi'          => $row->posisi,
                'debit'           => (float) $row->debit,
                'kredit'          => (float) $row->kredit,
            ]);

            $lastTxId = $row->transaksi_id;
        }

        // Baris Grand Total
        $data->push([
            'no'              => '',
            'tanggal'         => '',
            'nomor_transaksi' => '',
            'keterangan'      => 'TOTAL JURNAL UMUM (' . $this->summary->status_label . ')',
            'pihak'           => '',
            'kode_akun'       => '',
            'nama_akun'       => '',
            'posisi'          => '',
            'debit'           => (float) $this->summary->total_debit,
            'kredit'          => (float) $this->summary->total_kredit,
        ]);

        return $data;
    }

    public function headings(): array
    {
        $periode = $this->meta['periode'] ?? 'Semua Periode';
        $printedAt = 'Dicetak pada: ' . Carbon::now()->translatedFormat('d F Y H:i') . ' WIB';

        return [
            ['LAPORAN JURNAL UMUM (GENERAL JOURNAL)'],
            ['AETHER ACADEMIC & RESEARCH - MODUL KEUANGAN'],
            ['Periode Transaksi:', $periode],
            ['Status Keseimbangan:', $this->summary->status_label],
            [$printedAt],
            [''],
            [
                'No',
                'Tanggal',
                'No. Bukti / Transaksi',
                'Keterangan & Uraian',
                'Pihak Terkait (Klien/Vendor)',
                'Kode Akun',
                'Nama Akun (CoA)',
                'Posisi',
                'Debit (Rp)',
                'Kredit (Rp)',
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => '#,##0.00',
            'J' => '#,##0.00',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // 1. Judul Laporan
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0F172A'));
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF475569'));

        // 2. Metadata Info
        $sheet->getStyle('A3:A5')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('B3:B5')->getFont()->setSize(9);

        // 3. Header Tabel (Row 7)
        $sheet->getStyle('A7:J7')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 10,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'], // Dark Slate Navy
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(7)->setRowHeight(26);

        // 4. Data Rows Styling & Borders
        $totalRows = count($this->entries);
        $lastDataRow = 7 + $totalRows;
        $totalRowIndex = $lastDataRow + 1;

        if ($totalRows > 0) {
            // Border untuk seluruh tabel
            $sheet->getStyle("A7:J{$totalRowIndex}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);

            // Alignment kolom
            $sheet->getStyle("A8:A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B8:B{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C8:C{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F8:F{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H8:H{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I8:J{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // 5. Total Row Styling
        $sheet->getStyle("A{$totalRowIndex}:J{$totalRowIndex}")->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => '0F172A'],
                'size'  => 10,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0'], // Soft Gray Accent
            ],
            'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_THIN],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE],
            ],
        ]);

        $sheet->mergeCells("A{$totalRowIndex}:H{$totalRowIndex}");
        $sheet->getStyle("A{$totalRowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("I{$totalRowIndex}:J{$totalRowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }

    public function title(): string
    {
        return 'Jurnal Umum';
    }
}
