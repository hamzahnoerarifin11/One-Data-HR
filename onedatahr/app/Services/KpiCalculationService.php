<?php

namespace App\Services;

class KpiCalculationService
{
    /**
     * Calculate score based on method.
     *
     * @param string $method 'positive', 'negative', 'progress'
     * @param float $target
     * @param float $realisasi
     * @param float $previousProgress
     * @return float
     */
    public function calculateScore($method, $target, $realisasi, $previousProgress = 0)
    {
        switch ($method) {
            case 'negative':
                return $this->calculateNegative($target, $realisasi);
            case 'progress':
                return $this->calculateProgress($target, $realisasi, $previousProgress);
            case 'positive':
            default:
                return $this->calculatePositive($target, $realisasi);
        }
    }

    /**
     * Target Angka (Target vs Realisasi)
     * Semakin tinggi realisasi, semakin bagus (sampai batas tertentu, misal 100-120%).
     */
    protected function calculatePositive($target, $realisasi)
    {
        if ($target <= 0) return 0; // Avoid division by zero
        
        $score = ($realisasi / $target) * 100;
        
        // Cap score at 100? Or let it go higher?
        // Usually KPI caps at 100, 110, or 120. 
        // For now, I'll return the raw percentage, clamping can be done in Controller or Policy.
        // Assuming loose cap for now.
        return min($score, 120); // Cap at 120%
    }

    /**
     * Target Negatif (Zero Defect / Minimize Error)
     * Semakin tinggi realisasi (error), semakin rendah nilai.
     * Formula: 100 - (Realisasi)
     * Atau: (Target / Realisasi) * 100 if we want a ratio (e.g. speed).
     * Based on user request: "100 - Penalti"
     */
    /**
     * Target Negatif (Zero Defect / Minimize Error / Speed)
     * Semakin KECIL realisasi, semakin BAIK.
     * 
     * Rules:
     * 1. Jika Realisasi <= Target, maka Score = 100% (Cap).
     * 2. Jika Realisasi > Target, kena Penalty.
     *    Rumus: 100 - (((Realisasi - Target) / Target) * 100)
     * 3. Score tidak boleh negatif (Min 0).
     */
    protected function calculateNegative($target, $realisasi)
    {
        // 1. Cap 100% if better or equal
        if ($realisasi <= $target) {
            return 100;
        }

        // Avoid division by zero
        if ($target == 0) {
            // If target is 0 (e.g. Zero Defect), any realization > 0 is bad.
            // How bad? 1 error might be 90? 
            // Standard formula divides by target, so undefined.
            // Let's assume strict penalty: 100 - (Realisasi * 10?) or just 0?
            // User formula implies target > 0.
            // Fallback for Target 0: Return 0 if real > 0.
            return 0; 
        }

        // 2. Penalty Formula
        // (Realisasi - Target) / Target * 100 = % Deviation
        $deviation = (($realisasi - $target) / $target) * 100;
        $score = 100 - $deviation;

        // 3. Min 0
        return max($score, 0);
    }

    /**
     * Target Progres (Project-based)
     * Menghitung kontribusi progres bulan ini.
     * @param mixed $target Target progress for THIS MONTH (e.g., 40% of total)
     * @param mixed $realisasi Cumulative progress (e.g. 40%)
     * @param mixed $previousProgress Previous month cumulative (e.g. 0%)
     */
    protected function calculateProgress($target, $realisasi, $previousProgress)
    {
        // Progres bulan ini = Cumulative Current - Cumulative Previous
        $actualThisMonth = $realisasi - $previousProgress;
        
        if ($target <= 0) return 0;

        // Score based on THIS MONTH's target
        $score = ($actualThisMonth / $target) * 100;
        
        return min($score, 120);
    }
}
