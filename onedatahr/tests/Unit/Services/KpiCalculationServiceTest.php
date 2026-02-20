<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\KpiCalculationService;

class KpiCalculationServiceTest extends TestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KpiCalculationService();
    }

    public function test_calculate_positive()
    {
        // Target 100, Real 100 -> 100
        $this->assertEquals(100, $this->service->calculateScore('positive', 100, 100));

        // Target 100, Real 50 -> 50
        $this->assertEquals(50, $this->service->calculateScore('positive', 100, 50));

        // Target 100, Real 120 -> 120 (Capped at 120 in service)
        $this->assertEquals(120, $this->service->calculateScore('positive', 100, 120));

        // Target 100, Real 200 -> 120 (Capped)
        $this->assertEquals(120, $this->service->calculateScore('positive', 100, 200));
        
        // Target 0 -> 0
        $this->assertEquals(0, $this->service->calculateScore('positive', 0, 100));
    }

    public function test_calculate_negative()
    {
        // Target 0 (Zero Defect), Real 0 -> 100
        $this->assertEquals(100, $this->service->calculateScore('negative', 0, 0));

        // Target 0, Real 1 -> 99
        $this->assertEquals(99, $this->service->calculateScore('negative', 0, 1));
        
        // Target 0, Real 10 -> 90
        $this->assertEquals(90, $this->service->calculateScore('negative', 0, 10));

        // Target 0, Real 100 -> 0
        $this->assertEquals(0, $this->service->calculateScore('negative', 0, 100));

        // Target 0, Real 150 -> 0 (Floor at 0)
        $this->assertEquals(0, $this->service->calculateScore('negative', 0, 150));
    }

    public function test_calculate_progress()
    {
        // Target 100, Prev 0, Real 40 -> 40
        $this->assertEquals(40, $this->service->calculateScore('progress', 100, 40, 0));

        // Target 100, Prev 40, Real 100 -> (100-40)/100 ? NO.
        // Wait, if I pass Real 100 (Cumulative), and Prev 40.
        // Actual work done = 60.
        // If Target is 100 (Full Project), then Score = 60/100 * 100 = 60.
        // This makes sense if the "Target" passed is "Total Project Target".
        $this->assertEquals(60, $this->service->calculateScore('progress', 100, 100, 40));
        
        // If Target is 60 (Remaining Work), Prev 40, Real 100.
        // Actual work done = 60.
        // Score = 60/60 * 100 = 100.
        $this->assertEquals(100, $this->service->calculateScore('progress', 60, 100, 40));
    }
}
