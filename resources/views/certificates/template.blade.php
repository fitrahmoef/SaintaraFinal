<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat {{ $certificate->nomor_sertifikat }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
        }

        .certificate-container {
            background: white;
            padding: 60px;
            border: 15px solid #667eea;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            position: relative;
        }

        .certificate-border {
            border: 3px solid #764ba2;
            padding: 40px;
            position: relative;
        }

        .ornament {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 2px solid #f0e68c;
            pointer-events: none;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 8px;
        }

        .subtitle {
            font-size: 18px;
            color: #764ba2;
            font-style: italic;
            margin-bottom: 5px;
        }

        .certificate-title {
            font-size: 42px;
            font-weight: bold;
            color: #2d3748;
            text-transform: uppercase;
            letter-spacing: 6px;
            margin: 30px 0;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }

        .awarded-to {
            font-size: 20px;
            color: #4a5568;
            margin-bottom: 15px;
            font-style: italic;
        }

        .recipient-name {
            font-size: 48px;
            font-weight: bold;
            color: #764ba2;
            margin: 20px 0;
            text-decoration: underline;
            text-decoration-color: #f0e68c;
            text-decoration-thickness: 3px;
        }

        .description {
            font-size: 18px;
            color: #4a5568;
            line-height: 1.8;
            margin: 30px 0;
            text-align: center;
        }

        .character-info {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
            border-left: 5px solid #667eea;
        }

        .character-type {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }

        .character-desc {
            font-size: 16px;
            color: #4a5568;
            line-height: 1.6;
        }

        .details {
            display: table;
            width: 100%;
            margin: 30px 0;
        }

        .detail-row {
            display: table-row;
        }

        .detail-label {
            display: table-cell;
            font-weight: bold;
            color: #2d3748;
            padding: 8px 0;
            width: 30%;
        }

        .detail-value {
            display: table-cell;
            color: #4a5568;
            padding: 8px 0;
        }

        .footer {
            margin-top: 60px;
            display: table;
            width: 100%;
        }

        .signature-section {
            display: table-cell;
            text-align: center;
            vertical-align: bottom;
            width: 50%;
        }

        .signature-line {
            border-top: 2px solid #2d3748;
            margin: 60px 40px 10px 40px;
        }

        .signature-name {
            font-weight: bold;
            color: #2d3748;
            font-size: 16px;
        }

        .signature-title {
            color: #4a5568;
            font-size: 14px;
            font-style: italic;
        }

        .certificate-number {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #718096;
        }

        .qr-code {
            text-align: center;
            margin-top: 20px;
        }

        .verification-url {
            font-size: 12px;
            color: #667eea;
            margin-top: 10px;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(102, 126, 234, 0.05);
            font-weight: bold;
            pointer-events: none;
            z-index: 0;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-border">
            <div class="ornament"></div>
            <div class="watermark">SAINTARA</div>

            <div class="header">
                <div class="logo">SAINTARA</div>
                <div class="subtitle">Platform Analisis Karakter & Pengembangan Diri</div>
            </div>

            <div style="text-align: center;">
                <div class="certificate-title">Sertifikat Analisis Karakter</div>

                <div class="awarded-to">Diberikan Kepada</div>

                <div class="recipient-name">{{ $customer->nama_lengkap }}</div>

                <div class="description">
                    Telah menyelesaikan <strong>{{ $test->nama_tes }}</strong>
                    dan berhasil mengidentifikasi karakteristik kepribadian secara komprehensif
                    melalui analisis mendalam yang meliputi numerologi nama, tanggal lahir,
                    dan karakteristik psikologis golongan darah.
                </div>

                <div class="character-info">
                    <div class="character-type">{{ $testResult->hasil_karakter }}</div>
                    <div class="character-desc">{{ $testResult->deskripsi_hasil }}</div>
                </div>

                <div class="details">
                    <div class="detail-row">
                        <div class="detail-label">Tanggal Test:</div>
                        <div class="detail-value">{{ $testResult->tanggal_tes->format('d F Y') }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Skor Hasil:</div>
                        <div class="detail-value">{{ $testResult->skor }}/100</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Durasi Test:</div>
                        <div class="detail-value">{{ $testResult->getDurationInMinutes() }} menit</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Tanggal Lahir:</div>
                        <div class="detail-value">{{ $customer->tanggal_lahir ? \Carbon\Carbon::parse($customer->tanggal_lahir)->format('d F Y') : '-' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Golongan Darah:</div>
                        <div class="detail-value">{{ $customer->golongan_darah ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="footer">
                <div class="signature-section">
                    <div class="signature-line"></div>
                    <div class="signature-name">Dr. Saintara</div>
                    <div class="signature-title">Direktur Utama</div>
                </div>
                <div class="signature-section">
                    <div class="signature-line"></div>
                    <div class="signature-name">Tim Analisis</div>
                    <div class="signature-title">Kepala Divisi Psikologi</div>
                </div>
            </div>

            <div class="certificate-number">
                <strong>Nomor Sertifikat:</strong> {{ $certificate->nomor_sertifikat }}<br>
                <strong>Diterbitkan pada:</strong> {{ $certificate->tanggal_terbit->format('d F Y') }}
            </div>

            @if($certificate->url_verifikasi)
            <div class="verification-url">
                Verifikasi sertifikat: {{ $certificate->url_verifikasi }}
            </div>
            @endif
        </div>
    </div>
</body>
</html>
