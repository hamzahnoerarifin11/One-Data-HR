<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TurnoverExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $onboardings;

    public function __construct($onboardings)
    {
        $this->onboardings = $onboardings;
    }

    public function collection()
    {
        return collect($this->onboardings->map(function ($item) {
            return [
                'Nama' => $item->kandidat->nama ?? '-',
                'Posisi' => $item->posisi->nama_posisi ?? '-',
                'Jadwal Kontrak' => $item->formatTanggal('jadwal_ttd_kontrak'),
                'Tanggal Resign' => $item->formatTanggal('tanggal_resign') ?? '-',
                'Masa Kerja (Bulan)' => $item->masa_kerja_bulan,
                'Masa Kerja (Hari)' => $item->masa_kerja_hari,
                'Status' => strtoupper($item->status_turnover ?? $item->status_turnover_auto),
                'Alasan Resign' => $item->alasan_resign ?? '-',
            ];
        }));
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Posisi',
            'Jadwal Kontrak',
            'Tanggal Resign',
            'Masa Kerja (Bulan)',
            'Masa Kerja (Hari)',
            'Status',
            'Alasan Resign',
        ];
    }
}
