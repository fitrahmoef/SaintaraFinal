<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'test_result_id',
        'nomor_sertifikat',
        'diterbitkan_oleh',
        'ttd_digital',
        'url_verifikasi',
        'format_file',
        'file_path',
        'is_active',
        'tanggal_terbit',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'tanggal_terbit' => 'datetime',
    ];

    public function testResult(): BelongsTo
    {
        return $this->belongsTo(TestResult::class);
    }

    public static function generateNomorSertifikat(): string
    {
        $year = now()->year;
        $lastCert = self::whereYear('tanggal_terbit', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastCert ? ((int) substr($lastCert->nomor_sertifikat, -5)) + 1 : 1;

        return sprintf('CERT-%d-%05d', $year, $number);
    }

    public function getDownloadUrl(): string
    {
        return route('certificates.download', $this->id);
    }
}
