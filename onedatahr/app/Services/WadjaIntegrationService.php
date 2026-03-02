<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\ConnectionException;

class WadjaIntegrationService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = env('WADJA_INSTITUTE_API_BASE_URL');
        $this->apiKey = env('WADJA_INSTITUTE_API_KEY');
    }

    /**
     * Melakukan HTTP GET Request untuk menarik data Kompetensi Pegawai.
     * Mengembalikan response array jika sukses, atau tipe null jika error.
     */
    public function fetchPegawaiCompetencies(): ?array
    {
        if (empty($this->apiKey)) {
            Log::error('Wadja Integration: API Key is missing in environment variables.');
            return null;
        }

        try {
            $request = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Accept'    => 'application/json',
            ])->timeout(10);
            
            // JIKA di lokal (APP_ENV=local), matikan cek SSL.
            // JIKA di server asli (APP_ENV=production), nyalakan cek SSL untuk keamanan.
            if (env('APP_ENV') === 'local') {
                $request->withoutVerifying();
            }

            $response = $request->get("{$this->baseUrl}/integration/pegawai-competencies");

            $response->throw();

            return $response->json();

        } catch (RequestException $e) {
            Log::error('Wadja Integration: Failed HTTP Response from Pegawai Competencies.', [
                'status_code' => $e->response->status(),
                'response_body' => $e->response->body()
            ]);
            return null;

        } catch (ConnectionException $e) {
            Log::error('Wadja Integration: Connection Failed or Timeout.', [
                'message' => $e->getMessage()
            ]);
            return null;

        } catch (\Exception $e) {
            Log::critical('Wadja Integration: Unexpected Exception.', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }
}
