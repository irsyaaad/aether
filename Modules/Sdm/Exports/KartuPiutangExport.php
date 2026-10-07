<?php
namespace Modules\Sdm\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KartuPiutangExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $history;
    protected $karyawan;

    public function __construct($history, $karyawan)
    {
        $this->history  = $history;
        $this->karyawan = $karyawan;
    }

    public function collection()
    {
        $data         = collect();
        $no           = 1;
        $runningSaldo = 0;

        foreach ($this->history as $item) {
            $runningSaldo += $item->debet - $item->kredit;
            $data->push([
                'no'         => $no++,
                'tanggal'    => \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y'),
                'nomor'      => $item->nomor,
                'keterangan' => $item->keterangan,
                'debet'      => $item->debet ?: 0,
                'kredit'     => $item->kredit ?: 0,
                'saldo'      => $runningSaldo,
            ]);
        }

        // Add Total row
        $totalDebet  = $this->history->sum('debet');
        $totalKredit = $this->history->sum('kredit');

        $data->push([
            'no'         => '',
            'tanggal'    => '',
            'nomor'      => '',
            'keterangan' => 'TOTAL',
            'debet'      => $totalDebet,
            'kredit'     => $totalKredit,
            'saldo'      => $totalDebet - $totalKredit,
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [
            ['KARTU PIUTANG KARYAWAN'],
            ['Nama Karyawan:', $this->karyawan->nama_lengkap],
            ['Jabatan:', $this->karyawan->jabatan],
            ['Divisi:', $this->karyawan->divisi],
            [''],
            [
                'No',
                'Tanggal',
                'Nomor Dokumen',
                'Keterangan',
                'Debet (Pinjam)',
                'Kredit (Bayar)',
                'Saldo Running',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        return [
            1        => ['font' => ['bold' => true, 'size' => 14]],
            6        => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']]],
            $lastRow => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']]],
        ];
    }

    public function title(): string
    {
        return 'Kartu Piutang - ' . $this->karyawan->nama_lengkap;
    }
}
