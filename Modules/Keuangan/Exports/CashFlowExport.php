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

class CashFlowExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithColumnFormatting
{
    protected array $cashFlow;
    protected array $meta;

    public function __construct(array $cashFlow, array $meta = [])
    {
        $this->cashFlow = $cashFlow;
        $this->meta = $meta;
    }

    public function collection()
    {
        $data = collect();

        // ==========================================
        // 1. ARUS KAS DARI AKTIVITAS OPERASIONAL
        // ==========================================
        $data->push([
            'kode_akun'    => '',
            'uraian'       => '[ 1. ARUS KAS DARI AKTIVITAS OPERASIONAL ]',
            'tipe_arus'    => '',
            'nominal'      => '',
        ]);

        // Penerimaan Operasional
        $data->push([
            'kode_akun'    => '',
            'uraian'       => 'A. Penerimaan Kas dari Operasional (Inflow)',
            'tipe_arus'    => '',
            'nominal'      => '',
        ]);
        if (empty($this->cashFlow['operasional']['inflows'])) {
            $data->push(['kode_akun' => '-', 'uraian' => '   Tidak ada penerimaan kas operasional', 'tipe_arus' => 'Inflow', 'nominal' => 0.0]);
        } else {
            foreach ($this->cashFlow['operasional']['inflows'] as $item) {
                $data->push([
                    'kode_akun' => $item['kode_akun'],
                    'uraian'    => '   ' . $item['nama_akun'] . ' (' . $item['kategori'] . ')',
                    'tipe_arus' => 'Inflow',
                    'nominal'   => (float) $item['total_nominal'],
                ]);
            }
        }
        $data->push([
            'kode_akun' => '',
            'uraian'    => 'Subtotal Penerimaan Kas Operasional',
            'tipe_arus' => '',
            'nominal'   => (float) $this->cashFlow['operasional']['total_inflow'],
        ]);

        // Pengeluaran Operasional
        $data->push([
            'kode_akun' => '',
            'uraian'    => 'B. Pengeluaran Kas untuk Operasional (Outflow)',
            'tipe_arus' => '',
            'nominal'   => '',
        ]);
        if (empty($this->cashFlow['operasional']['outflows'])) {
            $data->push(['kode_akun' => '-', 'uraian' => '   Tidak ada pengeluaran kas operasional', 'tipe_arus' => 'Outflow', 'nominal' => 0.0]);
        } else {
            foreach ($this->cashFlow['operasional']['outflows'] as $item) {
                $data->push([
                    'kode_akun' => $item['kode_akun'],
                    'uraian'    => '   (-) ' . $item['nama_akun'] . ' (' . $item['kategori'] . ')',
                    'tipe_arus' => 'Outflow',
                    'nominal'   => -1 * (float) $item['total_nominal'],
                ]);
            }
        }
        $data->push([
            'kode_akun' => '',
            'uraian'    => 'Subtotal Pengeluaran Kas Operasional',
            'tipe_arus' => '',
            'nominal'   => -1 * (float) $this->cashFlow['operasional']['total_outflow'],
        ]);

        // Arus Kas Bersih Operasional
        $data->push([
            'kode_akun' => '',
            'uraian'    => 'ARUS KAS BERSIH DARI AKTIVITAS OPERASIONAL',
            'tipe_arus' => '',
            'nominal'   => (float) $this->cashFlow['operasional']['net_operasional'],
        ]);

        $data->push(['kode_akun' => '', 'uraian' => '', 'tipe_arus' => '', 'nominal' => '']);

        // ==========================================
        // 2. ARUS KAS DARI AKTIVITAS INVESTASI
        // ==========================================
        $data->push([
            'kode_akun' => '',
            'uraian'    => '[ 2. ARUS KAS DARI AKTIVITAS INVESTASI ]',
            'tipe_arus' => '',
            'nominal'   => '',
        ]);
        if (empty($this->cashFlow['investasi']['inflows']) && empty($this->cashFlow['investasi']['outflows'])) {
            $data->push(['kode_akun' => '-', 'uraian' => '   Tidak ada transaksi aktivitas investasi', 'tipe_arus' => '-', 'nominal' => 0.0]);
        } else {
            foreach ($this->cashFlow['investasi']['inflows'] as $item) {
                $data->push([
                    'kode_akun' => $item['kode_akun'],
                    'uraian'    => '   Penjualan ' . $item['nama_akun'],
                    'tipe_arus' => 'Inflow',
                    'nominal'   => (float) $item['total_nominal'],
                ]);
            }
            foreach ($this->cashFlow['investasi']['outflows'] as $item) {
                $data->push([
                    'kode_akun' => $item['kode_akun'],
                    'uraian'    => '   (-) Perolehan ' . $item['nama_akun'],
                    'tipe_arus' => 'Outflow',
                    'nominal'   => -1 * (float) $item['total_nominal'],
                ]);
            }
        }
        $data->push([
            'kode_akun' => '',
            'uraian'    => 'ARUS KAS BERSIH DARI AKTIVITAS INVESTASI',
            'tipe_arus' => '',
            'nominal'   => (float) $this->cashFlow['investasi']['net_investasi'],
        ]);

        $data->push(['kode_akun' => '', 'uraian' => '', 'tipe_arus' => '', 'nominal' => '']);

        // ==========================================
        // 3. ARUS KAS DARI AKTIVITAS PENDANAAN
        // ==========================================
        $data->push([
            'kode_akun' => '',
            'uraian'    => '[ 3. ARUS KAS DARI AKTIVITAS PENDANAAN ]',
            'tipe_arus' => '',
            'nominal'   => '',
        ]);
        if (empty($this->cashFlow['pendanaan']['inflows']) && empty($this->cashFlow['pendanaan']['outflows'])) {
            $data->push(['kode_akun' => '-', 'uraian' => '   Tidak ada transaksi aktivitas pendanaan', 'tipe_arus' => '-', 'nominal' => 0.0]);
        } else {
            foreach ($this->cashFlow['pendanaan']['inflows'] as $item) {
                $data->push([
                    'kode_akun' => $item['kode_akun'],
                    'uraian'    => '   Penerimaan ' . $item['nama_akun'],
                    'tipe_arus' => 'Inflow',
                    'nominal'   => (float) $item['total_nominal'],
                ]);
            }
            foreach ($this->cashFlow['pendanaan']['outflows'] as $item) {
                $data->push([
                    'kode_akun' => $item['kode_akun'],
                    'uraian'    => '   (-) Penarikan/Pembayaran ' . $item['nama_akun'],
                    'tipe_arus' => 'Outflow',
                    'nominal'   => -1 * (float) $item['total_nominal'],
                ]);
            }
        }
        $data->push([
            'kode_akun' => '',
            'uraian'    => 'ARUS KAS BERSIH DARI AKTIVITAS PENDANAAN',
            'tipe_arus' => '',
            'nominal'   => (float) $this->cashFlow['pendanaan']['net_pendanaan'],
        ]);

        $data->push(['kode_akun' => '', 'uraian' => '', 'tipe_arus' => '', 'nominal' => '']);

        // ==========================================
        // 4. REKAPITULASI DAN SALDO KAS AKHIR
        // ==========================================
        $data->push([
            'kode_akun' => '',
            'uraian'    => '[ 4. REKAPITULASI DAN SALDO KAS & BANK ]',
            'tipe_arus' => '',
            'nominal'   => '',
        ]);
        $data->push([
            'kode_akun' => '',
            'uraian'    => 'KENAIKAN / (PENURUNAN) BERSIH KAS & BANK',
            'tipe_arus' => '',
            'nominal'   => (float) $this->cashFlow['rekapitulasi']['kenaikan_bersih'],
        ]);
        $data->push([
            'kode_akun' => '',
            'uraian'    => 'SALDO AWAL KAS & BANK (AWAL PERIODE)',
            'tipe_arus' => '',
            'nominal'   => (float) $this->cashFlow['rekapitulasi']['saldo_awal_kas'],
        ]);
        $data->push([
            'kode_akun' => '',
            'uraian'    => 'SALDO AKHIR KAS & BANK (AKHIR PERIODE)',
            'tipe_arus' => '',
            'nominal'   => (float) $this->cashFlow['rekapitulasi']['saldo_akhir_kas'],
        ]);

        return $data;
    }

    public function headings(): array
    {
        $periode = $this->meta['periode'] ?? 'Semua Periode';
        $printedAt = 'Dicetak pada: ' . Carbon::now()->translatedFormat('d F Y H:i') . ' WIB';

        return [
            ['LAPORAN ARUS KAS (CASH FLOW STATEMENT)'],
            ['AETHER ACADEMIC & RESEARCH - MODUL KEUANGAN'],
            ['Periode Transaksi:', $periode],
            ['Saldo Akhir Kas & Bank:', 'Rp ' . number_format($this->cashFlow['rekapitulasi']['saldo_akhir_kas'], 0, ',', '.')],
            [$printedAt],
            [''],
            [
                'Kode Akun Lawan',
                'Uraian Sumber / Penggunaan Dana',
                'Arah Arus',
                'Nominal (Rp)',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2:D2');
        $sheet->mergeCells('B3:D3');
        $sheet->mergeCells('B4:D4');
        $sheet->mergeCells('A5:D5');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0F172A'));
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF475569'));
        $sheet->getStyle('A3:A4')->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('A5')->getFont()->setItalic(true)->setSize(8)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF94A3B8'));

        $sheet->getStyle('A7:D7')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 9,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'], // Royal Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle("A7:D{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'E2E8F0'],
                ],
            ],
        ]);

        $sheet->getStyle("A8:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C8:C{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        for ($r = 8; $r <= $highestRow; $r++) {
            $txt = (string) $sheet->getCell("B{$r}")->getValue();
            if (str_starts_with($txt, '[') || str_contains($txt, 'ARUS KAS BERSIH') || str_contains($txt, 'Subtotal') || str_contains($txt, 'SALDO') || str_contains($txt, 'KENAIKAN')) {
                $sheet->getStyle("A{$r}:D{$r}")->getFont()->setBold(true);
                if (str_contains($txt, 'SALDO AKHIR KAS & BANK')) {
                    $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'DBEAFE'],
                        ],
                    ]);
                } elseif (str_starts_with($txt, '[')) {
                    $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
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
        ];
    }

    public function title(): string
    {
        return 'Arus Kas (Cash Flow)';
    }
}
