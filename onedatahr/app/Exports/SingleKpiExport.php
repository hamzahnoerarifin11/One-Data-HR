<?php

namespace App\Exports;

use App\Models\KpiAssessment;
use App\Models\KpiItem;
use App\Models\KpiScore;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
            'Target Smt1',
            'Realisasi Smt1',
            'Adjustment Real Smt1',
            'Total Target Smt2',
            'Total Real Smt2',
            'Adjustment Target Smt2',
            'Adjustment Real Smt2',
            'Skor Akhir',
        ];

        // Set headers
        foreach ($headers as $col => $header) {
            $sheet->setCellValue(chr(65 + $col) . '1', $header);
            $sheet->getStyle(chr(65 + $col) . '1')->getFont()->setBold(true);
        }

        // Data rows
        $row = 2;
        foreach ($kpi->items as $item) {
            $score = $item->scores->first();

            $sheet->setCellValue('A' . $row, $item->perspektif ?? '-');
            $sheet->setCellValue('B' . $row, $item->key_result_area ?? '-');
            $sheet->setCellValue('C' . $row, $item->key_performance_indicator ?? '-');
            $sheet->setCellValue('D' . $row, $item->units ?? '-');
            $sheet->setCellValue('E' . $row, $item->polaritas ?? '-');
            $sheet->setCellValue('F' . $row, $item->bobot ?? 0);
            $sheet->setCellValue('G' . $row, $item->target ?? 0);
            $sheet->setCellValue('H' . $row, $score->target_smt1 ?? 0);
            $sheet->setCellValue('I' . $row, $score->real_smt1 ?? 0);
            $sheet->setCellValue('J' . $row, $score->adjustment_real_smt1 ?? '-');
            $sheet->setCellValue('K' . $row, $score->total_target_smt2 ?? 0);
            $sheet->setCellValue('L' . $row, $score->total_real_smt2 ?? 0);
            $sheet->setCellValue('M' . $row, $score->adjustment_target_smt2 ?? '-');
            $sheet->setCellValue('N' . $row, $score->adjustment_real_smt2 ?? '-');
            $sheet->setCellValue('O' . $row, $score->skor_akhir ?? 0);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "KPI_{$kpi->karyawan->NIK}_{$kpi->tahun}_" . now()->timestamp . ".xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename);
    }
}
