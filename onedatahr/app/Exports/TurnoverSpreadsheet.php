<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class TurnoverSpreadsheet
{
    public static function download($onboardings, ?string $periode): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();

        /* ===============================
         | SHEET 1 : DATA TURNOVER
         =============================== */
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Turnover');

        $headers = [
            'Nama',
            'Posisi',
            'Jadwal Kontrak',
            'Tanggal Resign',
            'Masa Kerja (Bulan)',
            'Masa Kerja (Hari)',
            'Status',
            'Alasan Resign',
        ];

        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        foreach ($onboardings as $item) {
            $sheet->fromArray([
                $item->kandidat->nama ?? '-',
                $item->posisi->nama_posisi ?? '-',
                $item->formatTanggal('jadwal_ttd_kontrak'),
                $item->formatTanggal('tanggal_resign') ?? '-',
                $item->masa_kerja_bulan,
                $item->masa_kerja_hari,
                strtoupper($item->status_turnover_auto),
                $item->alasan_resign ?? '-',
            ], null, "A{$row}");
            $row++;
        }

        self::styleTable($sheet, 'A1:H' . ($row - 1));

        /* ===============================
         | SHEET 2 : RINGKASAN
         =============================== */
        $summary = $spreadsheet->createSheet();
        $summary->setTitle('Ringkasan');

        $total = $onboardings->count();
        $turnover = $onboardings->where('status_turnover_auto', 'turnover')->count();
        $lolos = $onboardings->where('status_turnover_auto', 'lolos')->count();
        $rate = $total > 0 ? round(($turnover / $total) * 100) : 0;

        $summary->fromArray([
            ['Keterangan', 'Nilai'],
            ['Total Karyawan', $total],
            ['Total Turnover', $turnover],
            ['Total Lolos', $lolos],
            ['Turnover Rate (%)', $rate],
        ], null, 'A1');

        self::styleTable($summary, 'A1:B5');

        /* ===============================
         | LOGIKA PERIODE (IDENTIK SISTEM)
         =============================== */
        $startMonth = 1;
        $endMonth   = 12;

        if ($periode) {
            [$tahun, $semester] = array_pad(explode('-', $periode), 2, null);

            if ((int)$semester === 1) {
                $startMonth = 1;
                $endMonth   = 6;
            } elseif ((int)$semester === 2) {
                $startMonth = 7;
                $endMonth   = 12;
            }
        }

        /* ===============================
         | DATA GRAFIK BULANAN (FILTERED)
         =============================== */
        $grafik = [];
        for ($m = $startMonth; $m <= $endMonth; $m++) {
            $grafik[$m] = 0;
        }

        foreach ($onboardings as $item) {
            if (
                $item->status_turnover_auto === 'turnover' &&
                $item->tanggal_resign &&
                Carbon::parse($item->tanggal_resign)
                    ->lt(Carbon::parse($item->jadwal_ttd_kontrak)->addMonths(3))
            ) {
                $bulan = Carbon::parse($item->tanggal_resign)->month;

                if ($bulan >= $startMonth && $bulan <= $endMonth) {
                    $grafik[$bulan]++;
                }
            }
        }

        $bulanLabel = [];
        $nilai = [];

        foreach ($grafik as $bulan => $totalBulan) {
            $bulanLabel[] = Carbon::create()->month($bulan)->translatedFormat('F');
            $nilai[] = $totalBulan;
        }

        $summary->fromArray($bulanLabel, null, 'D2');
        $summary->fromArray($nilai, null, 'D3');

        /* ===============================
         | GRAFIK LINE CHART (DINAMIS)
         =============================== */
        $endColIndex = 3 + count($bulanLabel);
        $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endColIndex);

        $labels = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                "Ringkasan!D2:{$endCol}2",
                null,
                count($bulanLabel)
            )
        ];

        $values = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                "Ringkasan!D3:{$endCol}3",
                null,
                count($nilai)
            )
        ];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($values) - 1),
            [],
            $labels,
            $values
        );

        $plot = new PlotArea(null, [$series]);

        $chart = new Chart(
            'Grafik Turnover',
            new Title('Grafik Turnover Bulanan'),
            new Legend(Legend::POSITION_RIGHT),
            $plot
        );

        $chart->setTopLeftPosition('A7');
        $chart->setBottomRightPosition('H20');

        $summary->addChart($chart);

        /* ===============================
         | RESPONSE
         =============================== */
        $periodeLabel = $periode ? str_replace('-', '_', $periode) : 'all';
        $filename = "turnover_{$periodeLabel}_" . now()->format('Ymd_His') . ".xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->setIncludeCharts(true);
            $writer->save('php://output');
        }, $filename);
    }

    private static function styleTable($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin'],
            ],
        ]);

        $sheet->getStyle(explode(':', $range)[0])->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => 'D1E9FF'],
            ],
        ]);

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}
