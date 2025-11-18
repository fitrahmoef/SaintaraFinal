<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    /**
     * Download certificate PDF
     */
    public function download($id)
    {
        $customer = auth()->user()->customer;

        if (!$customer) {
            return response()->json(['message' => 'Customer profile not found'], 404);
        }

        // Find certificate with test result
        $certificate = Certificate::with(['testResult.customer', 'testResult.test'])
            ->findOrFail($id);

        // Verify ownership
        if ($certificate->testResult->customer_id !== $customer->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check if PDF already generated and stored
        if ($certificate->file_path && Storage::exists($certificate->file_path)) {
            return Storage::download($certificate->file_path);
        }

        // Generate PDF
        $pdf = $this->generateCertificatePDF($certificate);

        // Save PDF to storage
        $filename = 'certificates/' . $certificate->nomor_sertifikat . '.pdf';
        Storage::put($filename, $pdf->output());

        // Update certificate with file path
        $certificate->update(['file_path' => $filename]);

        // Return PDF for download
        return $pdf->download($certificate->nomor_sertifikat . '.pdf');
    }

    /**
     * Download test result PDF
     */
    public function downloadTestResult($id)
    {
        $customer = auth()->user()->customer;

        if (!$customer) {
            return response()->json(['message' => 'Customer profile not found'], 404);
        }

        // Find test result
        $testResult = TestResult::with(['test', 'customer', 'certificate'])
            ->findOrFail($id);

        // Verify ownership
        if ($testResult->customer_id !== $customer->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Generate test result PDF
        $pdf = $this->generateTestResultPDF($testResult);

        // Return PDF for download
        $filename = 'Test_Result_' . $testResult->id . '_' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * View certificate online (HTML)
     */
    public function view($id)
    {
        $customer = auth()->user()->customer;

        if (!$customer) {
            return response()->json(['message' => 'Customer profile not found'], 404);
        }

        $certificate = Certificate::with(['testResult.customer', 'testResult.test'])
            ->findOrFail($id);

        // Verify ownership
        if ($certificate->testResult->customer_id !== $customer->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Return view
        return view('certificates.view', [
            'certificate' => $certificate,
            'testResult' => $certificate->testResult,
            'customer' => $certificate->testResult->customer,
        ]);
    }

    /**
     * Generate certificate PDF
     */
    private function generateCertificatePDF(Certificate $certificate)
    {
        $testResult = $certificate->testResult;
        $customer = $testResult->customer;

        $data = [
            'certificate' => $certificate,
            'testResult' => $testResult,
            'customer' => $customer,
            'test' => $testResult->test,
        ];

        $pdf = PDF::loadView('certificates.template', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf;
    }

    /**
     * Generate test result PDF
     */
    private function generateTestResultPDF(TestResult $testResult)
    {
        $customer = $testResult->customer;
        $analisis = $testResult->analisis;

        $data = [
            'testResult' => $testResult,
            'customer' => $customer,
            'test' => $testResult->test,
            'analisis' => $analisis,
            'certificate' => $testResult->certificate,
        ];

        $pdf = PDF::loadView('certificates.test-result', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    /**
     * Verify certificate (public endpoint)
     */
    public function verify($nomor_sertifikat)
    {
        $certificate = Certificate::where('nomor_sertifikat', $nomor_sertifikat)
            ->with(['testResult.customer', 'testResult.test'])
            ->first();

        if (!$certificate) {
            return response()->json([
                'valid' => false,
                'message' => 'Certificate not found'
            ], 404);
        }

        if (!$certificate->is_active) {
            return response()->json([
                'valid' => false,
                'message' => 'Certificate is inactive'
            ]);
        }

        return response()->json([
            'valid' => true,
            'certificate' => [
                'nomor' => $certificate->nomor_sertifikat,
                'penerima' => $certificate->testResult->customer->nama_lengkap,
                'test' => $certificate->testResult->test->nama_tes,
                'karakter' => $certificate->testResult->hasil_karakter,
                'tanggal_terbit' => $certificate->tanggal_terbit->format('d M Y'),
                'diterbitkan_oleh' => $certificate->diterbitkan_oleh,
            ]
        ]);
    }
}
