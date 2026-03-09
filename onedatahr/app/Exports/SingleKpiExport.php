<?php

namespace App\Exports;

use App\Models\KpiAssessment;
use App\Models\KpiItem;
use App\Models\KpiScore;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SingleKpiExport
{
    public static function download($kpiAssessmentId): StreamedResponse
    {
        $kpi = KpiAssessment::with(['karyawan', 'items.scores'])->findOrFail($kpiAssessmentId);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('KPI Data');

        // Headers
        $headers = [
            'Perspektif',
            'Key Result Area',
            'Key Performance Indicator',
            'Units',
            'Polaritas',
            'Bobot (%)',
            'Target',
            // Bulan Januari - Desember (Target & Real)
            'Target Jan',
            'Real Jan',
            'Target Feb',
            'Real Feb',
            'Target Mar',
            'Real Mar',
            'Target Apr',
            'Real Apr',
            'Target Mei',
            'Real Mei',
            'Target Jun',
            'Real Jun',
            'Target Jul',
            'Real Jul',
            'Target Aug',
            'Real Aug',
            'Target Sep',
            'Real Sep',
            'Target Okt',
            'Real Okt',
            'Target Nov',
            'Real Nov',
            'Target Des',
            'Real Des',
            // Adjustments
            'Adjustment Target Smt2',
            'Adjustment Real Smt2',
            'Adjustment Real Smt1',
            'Skor Akhir',
        ];

        // Set headers
        foreach ($headers as $colIndex => $header) {
            $cell = Coordinate::stringFromColumnIndex($colIndex + 1) . '1';
            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }

        // Data rows
        $row = 2;
        foreach ($kpi->items as $item) {
            $score = $item->scores->first();

            $col = 1;
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $row, $item->perspektif ?? '-');
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $row, $item->key_result_area ?? '-');
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $row, $item->key_performance_indicator ?? '-');
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $row, $item->units ?? '-');
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $row, $item->polaritas ?? '-');
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $row, $item->bobot ?? 0);
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $row, $item->target ?? 0);

            // Months jan-dec
            $months = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'des'];
            foreach ($months as $m) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $row, $score ? ($score->{'target_' . $m} ?? 0) : 0);
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $row, $score ? ($score->{'real_' . $m} ?? 0) : 0);
            }

            // Adjustments & final score
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $row, $score->adjustment_target_smt2 ?? '-');
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $row, $score->adjustment_real_smt2 ?? '-');
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $row, $score->adjustment_real_smt1 ?? '-');
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col++) . $row, $score->skor_akhir ?? 0);

            $row++;
        }

        // Auto-size columns
        $lastColIndex = count($headers);
        for ($i = 1; $i <= $lastColIndex; $i++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }

        $filename = "KPI_{$kpi->karyawan->NIK}_{$kpi->tahun}_" . now()->timestamp . ".xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename);
    }
}
