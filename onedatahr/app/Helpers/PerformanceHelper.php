<?php

namespace App\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PerformanceHelper
{
    /**
     * Calculate final score dari KPI dan KBI
     * Formula: KPI (70%) + KBI (30%)
     */
    public static function calculateFinalScore(float $kpiScore, float $kbiScore): float
    {
        return ($kpiScore * 0.7) + ($kbiScore * 0.3);
    }

    /**
     * Determine grade berdasarkan final score
     */
    public static function determineGrade(float $score): string
    {
        if ($score >= 89) return 'A';
        elseif ($score >= 79) return 'B';
        elseif ($score >= 69) return 'C';
        else return 'D';
    }

    /**
     * Get grade label dengan warna
     */
    public static function getGradeLabel(string $grade): array
    {
        $labels = [
            'A' => [
                'text' => 'Excellent',
                'color' => 'emerald',
                'bg_class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
            ],
            'B' => [
                'text' => 'Good',
                'color' => 'blue',
                'bg_class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'
            ],
            'C' => [
                'text' => 'Satisfactory',
                'color' => 'yellow',
                'bg_class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'
            ],
            'D' => [
                'text' => 'Below Standard',
                'color' => 'red',
                'bg_class' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
            ],
        ];

        return $labels[$grade] ?? $labels['D'];
    }

    /**
     * Calculate statistics dari collection
     */
    public static function calculateStatistics(Collection $data): array
    {
        $total = count($data);
        $belowStandard = $data->sum('is_below_standard');

        return [
            'total' => $total,
            'average' => $total > 0 ? $data->avg('final_score') : 0,
            'min' => $total > 0 ? $data->min('final_score') : 0,
            'max' => $total > 0 ? $data->max('final_score') : 0,
            'below_standard_count' => $belowStandard,
            'below_standard_percentage' => $total > 0 ? round(($belowStandard / $total) * 100, 2) : 0,
            'grade_distribution' => [
                'A' => $data->where('grade', 'A')->count(),
                'B' => $data->where('grade', 'B')->count(),
                'C' => $data->where('grade', 'C')->count(),
                'D' => $data->where('grade', 'D')->count(),
            ]
        ];
    }

    /**
     * Find anomalies dalam data
     */
    public static function findAnomalies(Collection $data): array
    {
        $anomalies = [];

        // Divisi dengan Grade D terbanyak
        $divisiGradeD = $data
            ->where('grade', 'D')
            ->groupBy('divisi')
            ->map(fn($items) => [
                'divisi' => $items->first()->divisi,
                'count' => count($items),
            ])
            ->sortByDesc('count')
            ->first();

        if ($divisiGradeD) {
            $anomalies['high_grade_d_division'] = $divisiGradeD;
        }

        // Divisi dengan average score terendah
        $divisiLowest = $data
            ->groupBy('divisi')
            ->map(fn($items) => [
                'divisi' => $items->first()->divisi,
                'avg_score' => round($items->avg('final_score'), 2),
                'count' => count($items),
            ])
            ->sortBy('avg_score')
            ->first();

        if ($divisiLowest) {
            $anomalies['lowest_avg_division'] = $divisiLowest;
        }

        // Divisi dengan average score tertinggi (best performer)
        $divisiBest = $data
            ->groupBy('divisi')
            ->map(fn($items) => [
                'divisi' => $items->first()->divisi,
                'avg_score' => round($items->avg('final_score'), 2),
                'count' => count($items),
            ])
            ->sortByDesc('avg_score')
            ->first();

        if ($divisiBest) {
            $anomalies['highest_avg_division'] = $divisiBest;
        }

        return $anomalies;
    }

    /**
     * Format data untuk export
     */
    public static function formatForExport(Collection $data, string $format = 'array'): array
    {
        $export = [];

        foreach ($data as $item) {
            $export[] = [
                'nama' => $item->nama,
                'nik' => $item->nik,
                'jabatan' => $item->jabatan,
                'divisi' => $item->divisi,
                'departemen' => $item->departemen,
                'perusahaan' => $item->perusahaan,
                'skor_kpi' => $item->skor_kpi,
                'skor_kbi' => $item->skor_kbi,
                'final_score' => $item->final_score_formatted,
                'grade' => $item->grade,
                'status' => $item->grade == 'D' ? 'Perlu Evaluasi' : 'Normal',
            ];
        }

        return $export;
    }

    /**
     * Get data untuk summary report
     */
    public static function generateSummaryReport(Collection $data, string $tahun): array
    {
        $stats = self::calculateStatistics($data);
        $anomalies = self::findAnomalies($data);
        $groupedByDivisi = $data->groupBy('divisi')->map(fn($items) => [
            'divisi' => $items->first()->divisi,
            'total' => count($items),
            'avg_score' => round($items->avg('final_score'), 2),
            'grade_distribution' => [
                'A' => $items->where('grade', 'A')->count(),
                'B' => $items->where('grade', 'B')->count(),
                'C' => $items->where('grade', 'C')->count(),
                'D' => $items->where('grade', 'D')->count(),
            ],
            'items' => $items,
        ]);

        return [
            'tahun' => $tahun,
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'generated_by' => Auth::user()->name ?? 'System',
            'statistics' => $stats,
            'anomalies' => $anomalies,
            'grouped_by_division' => $groupedByDivisi,
            'total_records' => count($data),
        ];
    }

    /**
     * Validate score range
     */
    public static function isValidScore(float $score): bool
    {
        return $score >= 0 && $score <= 100;
    }

    /**
     * Get score interpretation
     */
    public static function interpretScore(float $score): string
    {
        $grade = self::determineGrade($score);
        $interpretations = [
            'A' => 'Karyawan berkinerja sangat baik. Pertahankan performa dan beri kesempatan untuk berkembang lebih lanjut.',
            'B' => 'Karyawan berkinerja baik. Terus berikan feedback konstruktif untuk peningkatan.',
            'C' => 'Karyawan berkinerja memuaskan. Berikan dukungan dan mentoring untuk improvement.',
            'D' => 'Karyawan membutuhkan improvement urgent. Siapkan action plan dan intensive coaching.',
        ];

        return $interpretations[$grade] ?? '';
    }

    /**
     * Calculate growth trend (simplified)
     */
    public static function calculateGrowthTrend(float $currentScore, float $previousScore): array
    {
        $difference = $currentScore - $previousScore;
        $percentageChange = $previousScore > 0 ? (($difference / $previousScore) * 100) : 0;

        return [
            'difference' => round($difference, 2),
            'percentage_change' => round($percentageChange, 2),
            'trend' => match (true) {
                $difference > 0 => 'up',
                $difference < 0 => 'down',
                default => 'stable'
            },
            'interpretation' => $percentageChange > 0 ? 'Meningkat' : ($percentageChange < 0 ? 'Menurun' : 'Stabil'),
        ];
    }
}
