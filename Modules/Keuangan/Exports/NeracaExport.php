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

class NeracaExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithColumnFormatting
{
    protected array $neraca;
    protected array $meta;

    public function __construct(array $neraca, array $meta = [])
    {
        $this->neraca = $neraca;
        $this->meta = $meta;
    }

    public function collection()
    {
        $data = collect();

        // ==========================
        // 1. ASET (AKTIVA)
        // ==========================
        $data->push([
            'kode_akun'    => '',
            'nama_akun'    => '[ 1. ASET (AKTIVA) ]',
            'saldo_awal'   => '',
            'mutasi_debit' => '',
            'mutasi_kredit'=> '',
            'saldo_akhir'  => '',
        ]);

        // 1.1 Aset Lancar
        $data->push([
            'kode_akun'    => '',
            'nama_akun'    => 'A. ASET LANCAR',
            'saldo_awal'   => '',
            'mutasi_debit' => '',
            'mutasi_kredit'=> '',
            'saldo_akhir'  => '',
        ]);
        foreach ($this->neraca['aset']['aset_lancar']['accounts'] as $acc) {
            $data->push([
                'kode_akun'    => $acc->kode_akun,
                'nama_akun'    => '   ' . $acc->nama_akun,
                'saldo_awal'   => (float) $acc->saldo_awal,
                'mutasi_debit' => (float) $acc->mutasi_debit,
                'mutasi_kredit'=> (float) $acc->mutasi_kredit,
                'saldo_akhir'  => (float) $acc->saldo_akhir,
            ]);
        }
        $data->push([
            'kode_akun'    => '',
            'nama_akun'    => 'Subtotal Aset Lancar',
            'saldo_awal'   => '',
            'mutasi_debit' => '',
            'mutasi_kredit'=> '',
            'saldo_akhir'  => (float) $this->neraca['aset']['aset_lancar']['subtotal'],
        ]);

        // 1.2 Aset Tetap
        $data->push([
            'kode_akun'    => '',
            'nama_akun'    => 'B. ASET TETAP',
            'saldo_awal'   => '',
            'mutasi_debit' => '',
            'mutasi_kredit'=> '',
            'saldo_akhir'  => '',
        ]);
        foreach ($this->neraca['aset']['aset_tetap']['accounts'] as $acc) {
            $prefix = $acc->is_kontra ? '   (-) ' : '   ';
            $val = $acc->is_kontra ? -1 * (float) $acc->saldo_akhir : (float) $acc->saldo_akhir;
            $data->push([
                'kode_akun'    => $acc->kode_akun,
                'nama_akun'    => $prefix . $acc->nama_akun,
                'saldo_awal'   => (float) $acc->saldo_awal,
                'mutasi_debit' => (float) $acc->mutasi_debit,
                'mutasi_kredit'=> (float) $acc->mutasi_kredit,
                'saldo_akhir'  => $val,
            ]);
        }
        $data->push([
            'kode_akun'    => '',
            'nama_akun'    => 'Subtotal Aset Tetap (Nilai Buku)',
            'saldo_awal'   => '',
            'mutasi_debit' => '',
            'mutasi_kredit'=> '',
            'saldo_akhir'  => (float) $this->neraca['aset']['aset_tetap']['subtotal'],
        ]);

        // TOTAL ASET
        $data->push([
            'kode_akun'    => '',
            'nama_akun'    => 'TOTAL ASET (AKTIVA)',
            'saldo_awal'   => '',
            'mutasi_debit' => '',
            'mutasi_kredit'=> '',
            'saldo_akhir'  => (float) $this->neraca['aset']['total'],
        ]);

        $data->push(['kode_akun' => '', 'nama_akun' => '', 'saldo_awal' => '', 'mutasi_debit' => '', 'mutasi_kredit' => '', 'saldo_akhir' => '']);

        // ==========================
        // 2. KEWAJIBAN (LIABILITAS)
        // ==========================
        $data->push([
            'kode_akun'    => '',
            'nama_akun'    => '[ 2. KEWAJIBAN (LIABILITAS) ]',
            'saldo_awal'   => '',
            'mutasi_debit' => '',
            'mutasi_kredit'=> '',
            'saldo_akhir'  => '',
        ]);

        // 2.1 Kewajiban Lancar
        $data->push([
            'kode_akun'    => '',
            'nama_akun'    => 'A. KEWAJIBAN LANCAR (JANGKA PENDEK)',
            'saldo_awal'   => '',
            'mutasi_debit' => '',
            'mutasi_kredit'=> '',
            'saldo_akhir'  => '',
        ]);
        foreach ($this->neraca['kewajiban']['kewajiban_lancar']['accounts'] as $acc) {
            $data->push([
                'kode_akun'    => $acc->kode_akun,
                'nama_akun'    => '   ' . $acc->nama_akun,
                'saldo_awal'   => (float) $acc->saldo_awal,
                'mutasi_debit' => (float) $acc->mutasi_debit,
                'mutasi_kredit'=> (float) $acc->mutasi_kredit,
                'saldo_akhir'  => (float) $acc->saldo_akhir,
            ]);
        }
        $data->push([
            'kode_akun'    => '',
            'nama_akun'    => 'Subtotal Kewajiban Lancar',
            'saldo_awal'   => '',
            'mutasi_debit' => '',
            'mutasi_kredit'=> '',
            'saldo_akhir'  => (float) $this->neraca['kewajiban']['kewajiban_lancar']['subtotal'],
        ]);

        // 2.2 Kewajiban Jangka Panjang
        $data->push([
            'kode_akun'    => '',
            'nama_akun'    => 'B. KEWAJIBAN JANGKA PANJANG',
            'saldo_awal'   => '',
            'mutasi_debit' => '',
            'mutasi_kredit'=> '',
            'saldo_akhir'  => '',
        ]);
        foreach ($this->neraca['kewajiban']['kewajiban_jangka_panjang']['accounts'] as $acc) {
            $data->push([
                'kode_akun'    => $acc->kode_akun,
                'nama_akun'    => '   ' . $acc->nama_akun,
                'saldo_awal'   => (float) $acc->saldo_awal,
                'mutasi_debit' => (float) $acc->mutasi_debit,
                'mutasi_kredit'=> (float) $acc->mutasi_kredit,
                'saldo_akhir'  => (float) $acc->saldo_akhir,
            ]);
        }
        $data->push([
            'kode_akun'    => '',
            'nama_akun'    => 'Subtotal Kewajiban Jangka Panjang',
            'saldo_awal'   => '',
            'mutasi_debit' => '',
            'mutasi_kredit'=> '',
            'saldo_akhir'  => (float) $this->neraca['kewajiban']['kewajiban_jangka_panjang']['subtotal'],
        ]);

        // TOTAL KEWAJIBAN
        $data->push([
            'kode_akun'    => '',
            'nama_akun'    => 'TOTAL KEWAJIBAN',
            'saldo_awal'   => '',
            'mutasi_debit' => '',
            'mutasi_kredit'=> '',
            'saldo_akhir'  => (float) $this->neraca['kewajiban']['total'],
        ]);

        $data->push(['kode_akun' => '', 'nama_akun' => '', 'saldo_awal' => '', 'mutasi_debit' => '', 'mutasi_kredit' => '', 'saldo_akhir' => '']);

        // ==========================
        // 3. EKUITAS (MODAL)
        // ==========================
        $data->push([
            'kode_akun'    => '',
            'nama_akun'    => '[ 3. EKUITAS (MODAL) ]',
            'saldo_awal'   => '',
            'mutasi_debit' => '',
            'mutasi_kredit'=> '',
            'saldo_akhir'  => '',
        ]);
        foreach ($this->neraca['ekuitas']['accounts'] as $acc) {
            $prefix = ($acc->is_kontra ?? false) ? '   (-) ' : '   ';
            $val = ($acc->is_kontra ?? false) ? -1 * (float) $acc->saldo_akhir : (float) $acc->saldo_akhir;
            $data->push([
                'kode_akun'    => $acc->kode_akun,
                'nama_akun'    => $prefix . $acc->nama_akun,
                'saldo_awal'   => (float) $acc->saldo_awal,
                'mutasi_debit' => (float) $acc->mutasi_debit,
                'mutasi_kredit'=> (float) $acc->mutasi_kredit,
                'saldo_akhir'  => $val,
            ]);
        }
        $data->push([
            'kode_akun'    => '',
            'nama_akun'    => 'TOTAL EKUITAS',
            'saldo_awal'   => '',
            'mutasi_debit' => '',
            'mutasi_kredit'=> '',
            'saldo_akhir'  => (float) $this->neraca['ekuitas']['total'],
        ]);

        // TOTAL KEWAJIBAN & EKUITAS (PASIVA)
        $data->push([
            'kode_akun'    => '',
            'nama_akun'    => 'TOTAL PASIVA (KEWAJIBAN + EKUITAS)',
            'saldo_awal'   => '',
            'mutasi_debit' => '',
            'mutasi_kredit'=> '',
            'saldo_akhir'  => (float) $this->neraca['total_pasiva'],
        ]);

        return $data;
    }

    public function headings(): array
    {
        $periode = $this->meta['periode'] ?? 'Semua Periode';
        $printedAt = 'Dicetak pada: ' . Carbon::now()->translatedFormat('d F Y H:i') . ' WIB';

        return [
            ['LAPORAN NERACA KEUANGAN (BALANCE SHEET)'],
            ['AETHER ACADEMIC & RESEARCH - MODUL KEUANGAN'],
            ['Periode / Per Tanggal:', $periode],
            ['Status Keseimbangan:', $this->neraca['status_label'] . ' (Selisih: Rp ' . number_format($this->neraca['selisih'], 0, ',', '.') . ')'],
            [$printedAt],
            [''],
            [
                'Kode Akun',
                'Nama Akun & Kategori',
                'Saldo Awal',
                'Mutasi Debit',
                'Mutasi Kredit',
                'Saldo Akhir (Posisi)',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header Judul Laporan
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->mergeCells('B3:F3');
        $sheet->mergeCells('B4:F4');
        $sheet->mergeCells('A5:F5');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0F172A'));
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF475569'));
        $sheet->getStyle('A3:A4')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('A5')->getFont()->setItalic(true)->setSize(8)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF94A3B8'));

        // Header Tabel Kolom (Baris 7)
        $sheet->getStyle('A7:F7')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 9,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'], // Navy Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $highestRow = $sheet->getHighestRow();

        // Border seluruh isi tabel
        $sheet->getStyle("A7:F{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'E2E8F0'],
                ],
            ],
        ]);

        // Alignment kolom kode akun di tengah
        $sheet->getStyle("A8:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Highlight baris-baris total & subtotal
        for ($r = 8; $r <= $highestRow; $r++) {
            $txt = (string) $sheet->getCell("B{$r}")->getValue();
            if (str_starts_with($txt, '[') || str_contains($txt, 'TOTAL') || str_contains($txt, 'Subtotal')) {
                $sheet->getStyle("A{$r}:F{$r}")->getFont()->setBold(true);
                if (str_contains($txt, 'TOTAL ASET') || str_contains($txt, 'TOTAL PASIVA')) {
                    $sheet->getStyle("A{$r}:F{$r}")->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'DBEAFE'], // Light Blue
                        ],
                    ]);
                } elseif (str_starts_with($txt, '[')) {
                    $sheet->getStyle("A{$r}:F{$r}")->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F1F5F9'], // Slate 100
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
            'C' => '#,##0.00',
            'D' => '#,##0.00',
            'E' => '#,##0.00',
            'F' => '#,##0.00',
        ];
    }

    public function title(): string
    {
        return 'Neraca Keuangan';
    }
}
