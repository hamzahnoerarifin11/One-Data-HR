<?php

namespace App\Exports;

class PerformanceRekapPDF
{
    protected $data;
    protected $summary;
    protected $tahun;
    protected $filters;
    protected $groupedByDivisi;

    public function __construct($data, $summary, $tahun, $groupedByDivisi, $filters = [])
    {
        $this->data = $data;
        $this->summary = $summary;
        $this->tahun = $tahun;
        $this->groupedByDivisi = $groupedByDivisi;
        $this->filters = $filters;
    }

    /**
     * Generate HTML untuk PDF
     */
    public function generateHTML(): string
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laporan Penilaian Kinerja Tahun ' . $this->tahun . '</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; font-size: 10pt; color: #333; }
                
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 3px solid #2563EB;
                    padding-bottom: 15px;
                }
                
                .header h1 {
                    font-size: 18pt;
                    color: #1F2937;
                    margin-bottom: 5px;
                }
                
                .header p {
                    color: #6B7280;
                    font-size: 11pt;
                }
                
                .summary-section {
                    margin: 25px 0;
                    page-break-inside: avoid;
                }
                
                .summary-section h2 {
                    font-size: 12pt;
                    color: #1F2937;
                    margin-bottom: 10px;
                    border-left: 4px solid #2563EB;
                    padding-left: 10px;
                }
                
                .summary-grid {
                    display: table;
                    width: 100%;
                    margin-bottom: 20px;
                }
                
                .summary-item {
                    display: table-cell;
                    width: 25%;
                    border: 1px solid #E5E7EB;
                    padding: 12px;
                    text-align: center;
                }
                
                .summary-item label {
                    font-size: 9pt;
                    color: #6B7280;
                    display: block;
                    margin-bottom: 5px;
                    font-weight: 600;
                }
                
                .summary-item .value {
                    font-size: 16pt;
                    font-weight: bold;
                    color: #2563EB;
                }
                
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                    margin-bottom: 20px;
                }
                
                table thead {
                    background-color: #2563EB;
                    color: white;
                }
                
                table th {
                    padding: 10px;
                    text-align: left;
                    font-weight: 600;
                    font-size: 10pt;
                    border: 1px solid #E5E7EB;
                }
                
                table td {
                    padding: 8px 10px;
                    border: 1px solid #E5E7EB;
                    font-size: 9pt;
                }
                
                table tbody tr:nth-child(even) {
                    background-color: #F9FAFB;
                }
                
                .divisi-header {
                    background-color: #1E40AF;
                    color: white;
                    padding: 12px;
                    margin-top: 20px;
                    margin-bottom: 10px;
                    font-weight: bold;
                    page-break-inside: avoid;
                }
                
                .grade-a { color: #16A34A; font-weight: bold; }
                .grade-b { color: #2563EB; font-weight: bold; }
                .grade-c { color: #D97706; font-weight: bold; }
                .grade-d { color: #DC2626; font-weight: bold; }
                
                .footer {
                    text-align: center;
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 1px solid #E5E7EB;
                    color: #6B7280;
                    font-size: 9pt;
                }
                
                .page-break {
                    page-break-after: always;
                }
            </style>
        </head>
        <body>
        ';

        // Header
        $html .= '
            <div class="header">
                <h1>📊 LAPORAN PENILAIAN KINERJA KARYAWAN</h1>
                <p>Tahun ' . $this->tahun . '</p>
            </div>
        ';

        // Executive Summary
        $html .= '
            <div class="summary-section">
                <h2>Ringkasan Eksekutif</h2>
                <div class="summary-grid">
                    <div class="summary-item">
                        <label>Total Dinilai</label>
                        <div class="value">' . ($this->summary['totalKaryawan'] ?? 0) . '</div>
                    </div>
                    <div class="summary-item">
                        <label>Rata-rata Score</label>
                        <div class="value">' . number_format($this->summary['avgFinalScore'] ?? 0, 2) . '</div>
                    </div>
                    <div class="summary-item">
                        <label>Grade A</label>
                        <div class="value">' . ($this->summary['gradeDistribution']['A'] ?? 0) . '</div>
                    </div>
                    <div class="summary-item">
                        <label>Grade D</label>
                        <div class="value">' . ($this->summary['gradeDistribution']['D'] ?? 0) . '</div>
                    </div>
                </div>
            </div>
        ';

        // Detailed data grouped by divisi
        $html .= '<div class="summary-section"><h2>Detail Penilaian Per Divisi</h2>';

        if ($this->groupedByDivisi && count($this->groupedByDivisi) > 0) {
            foreach ($this->groupedByDivisi as $group) {
                $html .= '<div class="divisi-header">' . $group['divisi'] . ' (Total: ' . $group['total'] . ' | Rata-rata: ' . $group['avg_score'] . ')</div>';

                $html .= '
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>KPI (70%)</th>
                                <th>KBI (30%)</th>
                                <th>Final Score</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                ';

                foreach ($group['items'] as $item) {
                    $gradeClass = 'grade-' . strtolower($item->grade);
                    $html .= '
                        <tr>
                            <td>' . $item->nama . '</td>
                            <td>' . $item->nik . '</td>
                            <td>' . $item->skor_kpi . '</td>
                            <td>' . $item->skor_kbi . '</td>
                            <td>' . $item->final_score_formatted . '</td>
                            <td><span class="' . $gradeClass . '">' . $item->grade . '</span></td>
                        </tr>
                    ';
                }

                $html .= '
                        </tbody>
                    </table>
                ';
            }
        }

        $html .= '</div>';

        // Footer
        $html .= '
            <div class="footer">
                <p>Laporan ini di-generate otomatis pada ' . date('d-m-Y H:i:s') . '</p>
                <p>Confidential - Hanya untuk penggunaan internal</p>
            </div>
        ';

        $html .= '
        </body>
        </html>
        ';

        return $html;
    }
}
