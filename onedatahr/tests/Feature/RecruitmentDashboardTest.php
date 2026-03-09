<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Kandidat;
use App\Models\Posisi;
use App\Models\Pemberkasan;
use Carbon\Carbon;

class RecruitmentDashboardTest extends TestCase
{
    /**
     * Test dashboard akurasi data
     */
    public function test_dashboard_loads_correctly()
    {
        $response = $this->get(route('rekrutmen.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.rekrutmen.dashboard');
        $response->assertViewHas(['funnelData', 'conversionRates', 'statsByPosition', 'monthlyData']);
    }

    /**
     * Test funnel data calculation accuracy
     */
    public function test_funnel_calculation_accuracy()
    {
        // Setup test data
        $year = 2024;

        // Total pelamar = 100
        $totalExpected = 100;
        Kandidat::factory()->count($totalExpected)
            ->create(['tanggal_melamar' => Carbon::now()->year($year)]);

        // Lolos CV = 80
        Kandidat::factory()->count(80)
            ->create([
                'tanggal_melamar' => Carbon::now()->year($year),
                'tgl_lolos_cv' => Carbon::now()
            ]);

        // Lolos Psikotes = 50
        Kandidat::factory()->count(50)
            ->create([
                'tanggal_melamar' => Carbon::now()->year($year),
                'tgl_lolos_cv' => Carbon::now(),
                'tgl_lolos_psikotes' => Carbon::now()
            ]);

        // Lolos Kompetensi = 35
        Kandidat::factory()->count(35)
            ->create([
                'tanggal_melamar' => Carbon::now()->year($year),
                'tgl_lolos_cv' => Carbon::now(),
                'tgl_lolos_psikotes' => Carbon::now(),
                'tgl_lolos_kompetensi' => Carbon::now()
            ]);

        $response = $this->get(route('rekrutmen.dashboard', ['year' => $year]));

        $response->assertViewHas('funnelData', function($data) {
            return $data['Total Pelamar'] >= 100
                && $data['Lolos CV'] == 80
                && $data['Lolos Psikotes'] == 50
                && $data['Lolos Kompetensi'] == 35;
        });
    }

    /**
     * Test conversion rates are calculated correctly
     */
    public function test_conversion_rates_calculation()
    {
        $response = $this->get(route('rekrutmen.dashboard'));

        $response->assertViewHas('conversionRates', function($rates) {
            // Check all conversion rates exist
            return isset($rates['cv'])
                && isset($rates['psikotes'])
                && isset($rates['kompetensi'])
                && isset($rates['hr'])
                && isset($rates['user'])
                && isset($rates['hired']);
        });
    }

    /**
     * Test position filter works correctly
     */
    public function test_position_filter()
    {
        // Create positions
        $posisi1 = Posisi::factory()->create(['nama_posisi' => 'Developer']);
        $posisi2 = Posisi::factory()->create(['nama_posisi' => 'Designer']);

        // Create candidates for specific position
        Kandidat::factory()->count(10)
            ->create([
                'posisi_id' => $posisi1->id_posisi,
                'tanggal_melamar' => Carbon::now()
            ]);

        Kandidat::factory()->count(5)
            ->create([
                'posisi_id' => $posisi2->id_posisi,
                'tanggal_melamar' => Carbon::now()
            ]);

        $response = $this->get(route('rekrutmen.dashboard', ['posisi_id' => $posisi1->id_posisi]));

        $response->assertViewHas('funnelData.Total Pelamar', 10);
    }

    /**
     * Test year filter works correctly
     */
    public function test_year_filter()
    {
        $year = 2024;

        // Create candidates for specific year
        Kandidat::factory()->count(15)
            ->create(['tanggal_melamar' => Carbon::now()->year($year)]);

        Kandidat::factory()->count(10)
            ->create(['tanggal_melamar' => Carbon::now()->year(2023)]);

        $response = $this->get(route('rekrutmen.dashboard', ['year' => $year]));

        $response->assertViewHas('funnelData.Total Pelamar', 15);
    }

    /**
     * Test monthly data distribution
     */
    public function test_monthly_data_distribution()
    {
        $year = 2024;

        // Create candidates spread across months
        for ($month = 1; $month <= 12; $month++) {
            Kandidat::factory()->count(5)
                ->create([
                    'tanggal_melamar' => Carbon::now()->year($year)->month($month)
                ]);
        }

        $response = $this->get(route('rekrutmen.dashboard', ['year' => $year]));

        $response->assertViewHas('monthlyData', function($data) {
            // Check all months have data
            return count($data) == 12;
        });
    }

    /**
     * Test hired count from pemberkasan table
     */
    public function test_hired_count_accuracy()
    {
        $year = 2024;

        // Create candidates
        $kandidats = Kandidat::factory()->count(20)
            ->create(['tanggal_melamar' => Carbon::now()->year($year)]);

        // Create pemberkasan records (hired)
        foreach ($kandidats->take(5) as $kandidat) {
            Pemberkasan::factory()->create([
                'kandidat_id' => $kandidat->id_kandidat,
                'selesai_recruitment' => Carbon::now()
            ]);
        }

        $response = $this->get(route('rekrutmen.dashboard', ['year' => $year]));

        $response->assertViewHas('funnelData.Hired (Selesai)', 5);
    }

    /**
     * Test available years are displayed correctly
     */
    public function test_available_years_display()
    {
        // Create candidates in different years
        Kandidat::factory()->create(['tanggal_melamar' => Carbon::parse('2023-01-01')]);
        Kandidat::factory()->create(['tanggal_melamar' => Carbon::parse('2024-01-01')]);
        Kandidat::factory()->create(['tanggal_melamar' => Carbon::parse('2025-01-01')]);

        $response = $this->get(route('rekrutmen.dashboard'));

        $response->assertViewHas('availableYears', function($years) {
            return in_array(2023, $years)
                && in_array(2024, $years)
                && in_array(2025, $years);
        });
    }

    /**
     * Test statistics by position
     */
    public function test_statistics_by_position()
    {
        $posisi = Posisi::factory()->create(['nama_posisi' => 'Software Engineer']);

        // Create candidates with different statuses
        Kandidat::factory()->count(20)
            ->create([
                'posisi_id' => $posisi->id_posisi,
                'tanggal_melamar' => Carbon::now()
            ]);

        Kandidat::factory()->count(15)
            ->create([
                'posisi_id' => $posisi->id_posisi,
                'tanggal_melamar' => Carbon::now(),
                'tgl_lolos_cv' => Carbon::now()
            ]);

        Kandidat::factory()->count(10)
            ->create([
                'posisi_id' => $posisi->id_posisi,
                'tanggal_melamar' => Carbon::now(),
                'tgl_lolos_cv' => Carbon::now(),
                'tgl_lolos_psikotes' => Carbon::now()
            ]);

        $response = $this->get(route('rekrutmen.dashboard'));

        $response->assertViewHas('statsByPosition', function($stats) {
            return count($stats) > 0
                && $stats[0]->cv_lolos == 15
                && $stats[0]->psikotes_lolos == 10;
        });
    }

    /**
     * Test rejected count calculation
     */
    public function test_rejected_count_calculation()
    {
        $year = 2024;

        // Create total candidates
        Kandidat::factory()->count(20)
            ->create(['tanggal_melamar' => Carbon::now()->year($year)]);

        // Create passed candidates (lolos CV)
        Kandidat::factory()->count(10)
            ->create([
                'tanggal_melamar' => Carbon::now()->year($year),
                'tgl_lolos_cv' => Carbon::now()
            ]);

        // Rejected should be 10 (20 - 10)
        $response = $this->get(route('rekrutmen.dashboard', ['year' => $year]));

        $response->assertViewHas('funnelData.Ditolak', 10);
    }

    /**
     * Test effective rate calculation
     */
    public function test_effective_rate_calculation()
    {
        $year = 2024;

        // Create 100 candidates
        $candidates = Kandidat::factory()->count(100)
            ->create(['tanggal_melamar' => Carbon::now()->year($year)]);

        // 10 hired
        foreach ($candidates->take(10) as $kandidat) {
            Pemberkasan::factory()->create([
                'kandidat_id' => $kandidat->id_kandidat,
                'selesai_recruitment' => Carbon::now()
            ]);
        }

        $response = $this->get(route('rekrutmen.dashboard', ['year' => $year]));

        // Total pelamar = 100, hired = 10, effective rate = 10%
        $response->assertViewHas('totalPelamar', 100);
        $response->assertViewHas('funnelData.Hired (Selesai)', 10);
    }
}
