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

class RugiLabaExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithColumnFormatting
{
    protected array $report;
    protected array $meta;

    public function __construct(array $report, array $meta = [])
    {
        $this->report = $report;
        $this->meta = $meta;
    }

    public function collection()
    {
        $data = collect();

        // ==========================
        // 1. PENDAPATAN
        // ==========================
        $data->push([
            'kode_akun'     => '',
            'nama_akun'     => '[ 1. PENDAPATAN (REVENUE) ]',
            'kategori'      => '',
            'mutasi_debit'  => '',
            'mutasi_kredit' => '',
            'nominal'       => '',
        ]);

        foreach ($this->report['pendapatan']['categories'] as $cat) {
            $data->push([
                'kode_akun'     => '',
                'nama_akun'     => 'Kategori: ' . $cat['kategori'],
                'kategori'      => '',
                'mutasi_debit'  => '',
                'mutasi_kredit' => '',
                'nominal'       => '',
            ]);

            foreach ($cat['accounts'] as $acc) {
                $data->push([
                    'kode_akun'     => $acc->kode_akun,
                    'nama_akun'     => '   ' . $acc->nama_akun,
                    'kategori'      => $acc->kategori,
                    'mutasi_debit'  => (float) $acc->mutasi_debit,
                    'mutasi_kredit' => (float) $acc->mutasi_kredit,
                    'nominal'       => (float) $acc->nominal,
                ]);
            }

            $data->push([
                'kode_akun'     => '',
                'nama_akun'     => 'Subtotal ' . $cat['kategori'],
                'kategori'      => '',
                'mutasi_debit'  => '',
                'mutasi_kredit' => '',
                'nominal'       => (float) $cat['subtotal'],
            ]);
        }

        // TOTAL PENDAPATAN
        $data->push([
            'kode_akun'     => '',
            'nama_akun'     => 'TOTAL PENDAPATAN',
            'kategori'      => '',
            'mutasi_debit'  => '',
            'mutasi_kredit' => '',
            'nominal'       => (float) $this->report['total_pendapatan'],
        ]);

        $data->push(['kode_akun' => '', 'nama_akun' => '', 'kategori' => '', 'mutasi_debit' => '', 'mutasi_kredit' => '', 'nominal' => '']);

        // ==========================
        // 2. BEBAN OPERASIONAL
        // ==========================
        $data->push([
            'kode_akun'     => '',
            'nama_akun'     => '[ 2. BEBAN OPERASIONAL (EXPENSES) ]',
            'kategori'      => '',
            'mutasi_debit'  => '',
            'mutasi_kredit' => '',
            'nominal'       => '',
        ]);

        foreach ($this->report['beban']['categories'] as $cat) {
            $data->push([
                'kode_akun'     => '',
                'nama_akun'     => 'Kategori: ' . $cat['kategori'],
                'kategori'      => '',
                'mutasi_debit'  => '',
                'mutasi_kredit' => '',
                'nominal'       => '',
            ]);

            foreach ($cat['accounts'] as $acc) {
                $data->push([
                    'kode_akun'     => $acc->kode_akun,
                    'nama_akun'     => '   ' . $acc->nama_akun,
                    'kategori'      => $acc->kategori,
                    'mutasi_debit'  => (float) $acc->mutasi_debit,
                    'mutasi_kredit' => (float) $acc->mutasi_kredit,
                    'nominal'       => (float) $acc->nominal,
                ]);
            }

            $data->push([
                'kode_akun'     => '',
                'nama_akun'     => 'Subtotal ' . $cat['kategori'],
                'kategori'      => '',
                'mutasi_debit'  => '',
                'mutasi_kredit' => '',
                'nominal'       => (float) $cat['subtotal'],
            ]);
        }

        // TOTAL BEBAN
        $data->push([
            'kode_akun'     => '',
            'nama_akun'     => 'TOTAL BEBAN OPERASIONAL',
            'kategori'      => '',
            'mutasi_debit'  => '',
            'mutasi_kredit' => '',
            'nominal'       => (float) $this->report['total_beban'],
        ]);

        $data->push(['kode_akun' => '', 'nama_akun' => '', 'kategori' => '', 'mutasi_debit' => '', 'mutasi_kredit' => '', 'nominal' => '']);

        // ==========================
        // 3. LABA / (RUGI) BERSIH
        // ==========================
        $data->push([
            'kode_akun'     => '',
            'nama_akun'     => strtoupper($this->report['status_label']) . ' (NET PROFIT / LOSS)',
            'kategori'      => '',
            'mutasi_debit'  => '',
            'mutasi_kredit' => '',
            'nominal'       => (float) $this->report['laba_bersih'],
        ]);

        return $data;
    }

    public function headings(): array
    {
        $periode = $this->meta['periode'] ?? 'Semua Periode';
        $printedAt = 'Dicetak pada: ' . Carbon::now()->translatedFormat('d F Y H:i') . ' WIB';

        return [
            ['LAPORAN LABA RUGI (INCOME STATEMENT)'],
            ['AETHER ACADEMIC & RESEARCH - MODUL KEUANGAN'],
            ['Periode Transaksi:', $periode],
            ['Hasil Usaha:', $this->report['status_label'] . ' (Rp ' . number_format($this->report['laba_bersih'], 0, ',', '.') . ')'],
            [$printedAt],
            [''],
            [
                'Kode Akun',
                'Uraian Akun & Kategori',
                'Klasifikasi',
                'Mutasi Debit',
                'Mutasi Kredit',
                'Total Nominal (Rp)',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->mergeCells('B3:F3');
        $sheet->mergeCells('B4:F4');
        $sheet->mergeCells('A5:F5');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0F172A'));
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF475569'));
        $sheet->getStyle('A3:A4')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('A5')->getFont()->setItalic(true)->setSize(8)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF94A3B8'));

        $sheet->getStyle('A7:F7')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 9,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'], // Emerald Green
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle("A7:F{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'E2E8F0'],
                ],
            ],
        ]);

        $sheet->getStyle("A8:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        for ($r = 8; $r <= $highestRow; $r++) {
            $txt = (string) $sheet->getCell("B{$r}")->getValue();
            if (str_starts_with($txt, '[') || str_contains($txt, 'TOTAL') || str_contains($txt, 'Subtotal') || str_contains($txt, 'LABA') || str_contains($txt, 'RUGI')) {
                $sheet->getStyle("A{$r}:F{$r}")->getFont()->setBold(true);
                if (str_contains($txt, 'LABA') || str_contains($txt, 'RUGI')) {
                    $sheet->getStyle("A{$r}:F{$r}")->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'D1FAE5'], // Light Emerald
                        ],
                    ]);
                } elseif (str_starts_with($txt, '[')) {
                    $sheet->getStyle("A{$r}:F{$r}")->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F1F5F9'],
                        ],
                    ]);
                }
            }
        }

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'D' => '#,##0.00',
            'E' => '#,##0.00',
            'F' => '#,##0.00',
        ];
    }

    public function title(): string
    {
        return 'Laba Rugi';
    }
}
