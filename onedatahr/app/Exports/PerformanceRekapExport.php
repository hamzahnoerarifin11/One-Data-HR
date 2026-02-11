<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\PatternFill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PerformanceRekapExport
{
    protected $data;
    protected $summary;
    protected $tahun;
    protected $filters;

    public function __construct($data, $summary, $tahun, $filters = [])
    {
        $this->data = $data;
        $this->summary = $summary;
        $this->tahun = $tahun;
        $this->filters = $filters;
    }

    /**
     * Generate Excel file
     */
    public function generate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Kinerja');

        $row = 1;

        // Header section dengan summary
        $sheet->setCellValue('A' . $row, 'LAPORAN PENILAIAN KINERJA KARYAWAN');
        $sheet->mergeCells('A' . $row . ':J' . $row);
        $this->styleHeader($sheet, $row);
        $row++;

        $sheet->setCellValue('A' . $row, 'Tahun: ' . $this->tahun);
        $sheet->mergeCells('A' . $row . ':J' . $row);
        $this->styleSubHeader($sheet, $row);
        $row += 2;

        // Summary stats
        $sheet->setCellValue('A' . $row, 'RINGKASAN EKSEKUTIF');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $this->styleSectionHeader($sheet, $row);
        $row++;

        $row = $this->addSummaryStat($sheet, $row, 'Total Karyawan Dinilai', $this->summary['totalKaryawan'] ?? 0);
        $row = $this->addSummaryStat($sheet, $row, 'Rata-rata Final Score', $this->summary['avgFinalScore'] ?? 0);
        $row = $this->addSummaryStat($sheet, $row, 'Grade A', $this->summary['gradeDistribution']['A'] ?? 0);
        $row = $this->addSummaryStat($sheet, $row, 'Grade B', $this->summary['gradeDistribution']['B'] ?? 0);
        $row = $this->addSummaryStat($sheet, $row, 'Grade C', $this->summary['gradeDistribution']['C'] ?? 0);
        $row = $this->addSummaryStat($sheet, $row, 'Grade D', $this->summary['gradeDistribution']['D'] ?? 0);

        $row += 2;

        // Detail data header
        $sheet->setCellValue('A' . $row, 'DETAIL PENILAIAN KARYAWAN');
        $sheet->mergeCells('A' . $row . ':J' . $row);
        $this->styleSectionHeader($sheet, $row);
        $row += 2;

        // Table header
        $headers = [
            'A' => 'Nama Lengkap',
            'B' => 'NIK',
            'C' => 'Divisi',
            'D' => 'Departemen',
            'E' => 'Perusahaan',
            'F' => 'KPI (70%)',
            'G' => 'KBI (30%)',
            'H' => 'Final Score',
            'I' => 'Grade',
            'J' => 'Status'
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . $row, $header);
        }
        $this->styleTableHeader($sheet, $row);
        $row++;

        // Table data
        foreach ($this->data as $item) {
            $sheet->setCellValue('A' . $row, $item->nama ?? '-');
            $sheet->setCellValue('B' . $row, $item->nik ?? '-');
            $sheet->setCellValue('C' . $row, $item->divisi ?? '-');
            $sheet->setCellValue('D' . $row, $item->departemen ?? '-');
            $sheet->setCellValue('E' . $row, $item->perusahaan ?? '-');
            $sheet->setCellValue('F' . $row, $item->skor_kpi ?? '-');
            $sheet->setCellValue('G' . $row, $item->skor_kbi ?? '-');
            $sheet->setCellValue('H' . $row, $item->final_score_formatted ?? '-');
            $sheet->setCellValue('I' . $row, $item->grade ?? '-');
            $sheet->setCellValue('J' . $row, ($item->grade == 'D' ? 'Perlu Evaluasi' : 'Standar'));

            $this->styleTableRow($sheet, $row);
            $row++;
        }

        $row += 2;
        $sheet->setCellValue('A' . $row, 'Tanggal Export');
        $sheet->setCellValue('B' . $row, date('d-m-Y H:i:s'));

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(10);
        $sheet->getColumnDimension('J')->setWidth(15);

        return $spreadsheet;
    }

    /**
     * Add summary statistic row
     */
    private function addSummaryStat($sheet, $row, $label, $value)
    {
        $sheet->setCellValue('A' . $row, $label);
        $sheet->setCellValue('B' . $row, $value);
        return $row + 1;
    }

    /**
     * Style main header
     */
    private function styleHeader(&$sheet, $row)
    {
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1F2937']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
    }

    /**
     * Style sub header
     */
    private function styleSubHeader(&$sheet, $row)
    {
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
            'font' => ['size' => 11, 'color' => ['rgb' => '6B7280']],
        ]);
    }

    /**
     * Style section header
     */
    private function styleSectionHeader(&$sheet, $row)
    {
        $sheet->getStyle('A' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1F2937']],
        ]);
    }

    /**
     * Style table header
     */
    private function styleTableHeader(&$sheet, $row)
    {
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => PatternFill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
    }

    /**
     * Style table row
     */
    private function styleTableRow(&$sheet, $row)
    {
        $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['bottom' => ['borderStyle' => 'thin']],
        ]);
    }
}
