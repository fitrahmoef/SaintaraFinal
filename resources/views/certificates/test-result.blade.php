<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Test - {{ $testResult->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            padding: 40px;
            background: #f7fafc;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 4px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            color: #2d3748;
            margin: 20px 0 10px 0;
        }

        .subtitle {
            font-size: 14px;
            color: #718096;
        }

        .section {
            margin: 30px 0;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin: 15px 0;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            color: #4a5568;
            padding: 8px 15px 8px 0;
            width: 35%;
        }

        .info-value {
            display: table-cell;
            color: #2d3748;
            padding: 8px 0;
        }

        .character-result {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            padding: 25px;
            border-radius: 8px;
            border-left: 5px solid #667eea;
            margin: 20px 0;
        }

        .character-name {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }

        .character-desc {
            font-size: 14px;
            color: #4a5568;
            line-height: 1.8;
        }

        .scores-container {
            display: table;
            width: 100%;
            margin: 20px 0;
        }

        .score-item {
            display: table-row;
        }

        .score-label {
            display: table-cell;
            padding: 10px 15px 10px 0;
            color: #4a5568;
            font-weight: 500;
            width: 40%;
        }

        .score-bar-container {
            display: table-cell;
            padding: 10px 0;
            width: 60%;
        }

        .score-bar {
            background: #e2e8f0;
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        .score-bar-fill {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            height: 100%;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 8px;
            color: white;
            font-size: 11px;
            font-weight: bold;
        }

        .strengths-list {
            list-style: none;
            padding: 0;
        }

        .strengths-list li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
            color: #2d3748;
        }

        .strengths-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #48bb78;
            font-weight: bold;
            font-size: 16px;
        }

        .development-list {
            list-style: none;
            padding: 0;
        }

        .development-list li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
            color: #2d3748;
        }

        .development-list li:before {
            content: "→";
            position: absolute;
            left: 0;
            color: #ed8936;
            font-weight: bold;
            font-size: 16px;
        }

        .career-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .career-tag {
            background: #667eea;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
        }

        .numerology-box {
            background: #f7fafc;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            margin: 10px 0;
        }

        .numerology-item {
            display: inline-block;
            margin-right: 30px;
        }

        .numerology-label {
            font-size: 12px;
            color: #718096;
            text-transform: uppercase;
        }

        .numerology-value {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            color: #718096;
            font-size: 12px;
        }

        .certificate-info {
            background: #f0fff4;
            border: 2px solid #48bb78;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
        }

        .certificate-number {
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
        }

        @media print {
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">SAINTARA</div>
            <div class="title">Laporan Hasil Analisis Karakter</div>
            <div class="subtitle">Platform Analisis Karakter & Pengembangan Diri</div>
        </div>

        <!-- Informasi Peserta -->
        <div class="section">
            <div class="section-title">Informasi Peserta</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Nama Lengkap:</div>
                    <div class="info-value">{{ $customer->nama_lengkap }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Lahir:</div>
                    <div class="info-value">{{ $customer->tanggal_lahir ? \Carbon\Carbon::parse($customer->tanggal_lahir)->format('d F Y') : '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Golongan Darah:</div>
                    <div class="info-value">{{ $customer->golongan_darah ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Jenis Kelamin:</div>
                    <div class="info-value">{{ ucfirst($customer->jenis_kelamin ?? '-') }}</div>
                </div>
            </div>
        </div>

        <!-- Informasi Test -->
        <div class="section">
            <div class="section-title">Informasi Test</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Nama Test:</div>
                    <div class="info-value">{{ $test->nama_tes }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Test:</div>
                    <div class="info-value">{{ $testResult->tanggal_tes->format('d F Y, H:i') }} WIB</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Durasi Pengerjaan:</div>
                    <div class="info-value">{{ $testResult->getDurationInMinutes() }} menit</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Skor Total:</div>
                    <div class="info-value"><strong>{{ $testResult->skor }}/100</strong></div>
                </div>
            </div>
        </div>

        <!-- Hasil Karakter -->
        <div class="section">
            <div class="section-title">Hasil Analisis Karakter</div>
            <div class="character-result">
                <div class="character-name">{{ $testResult->hasil_karakter }}</div>
                <div class="character-desc">{{ $testResult->deskripsi_hasil }}</div>
            </div>
        </div>

        <!-- Numerologi -->
        @if(isset($analisis['numerology']))
        <div class="section">
            <div class="section-title">Analisis Numerologi</div>
            <div class="numerology-box">
                <div class="numerology-item">
                    <div class="numerology-label">Name Number</div>
                    <div class="numerology-value">{{ $analisis['numerology']['name_number'] }}</div>
                </div>
                <div class="numerology-item">
                    <div class="numerology-label">Life Path Number</div>
                    <div class="numerology-value">{{ $analisis['numerology']['life_path_number'] }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Skor Detail -->
        @if(isset($analisis['detailed_scores']))
        <div class="section">
            <div class="section-title">Skor Kepribadian Detail</div>
            <div class="scores-container">
                @foreach($analisis['detailed_scores'] as $trait => $score)
                <div class="score-item">
                    <div class="score-label">{{ ucfirst(str_replace('_', ' ', $trait)) }}</div>
                    <div class="score-bar-container">
                        <div class="score-bar">
                            <div class="score-bar-fill" style="width: {{ $score }}%">{{ $score }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Kekuatan -->
        @if(isset($analisis['strengths']))
        <div class="section">
            <div class="section-title">Kekuatan Anda</div>
            <ul class="strengths-list">
                @foreach($analisis['strengths'] as $strength)
                <li>{{ $strength }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Area Pengembangan -->
        @if(isset($analisis['development_areas']))
        <div class="section">
            <div class="section-title">Area Pengembangan</div>
            <ul class="development-list">
                @foreach($analisis['development_areas'] as $area)
                <li>{{ $area }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Rekomendasi Karir -->
        @if(isset($analisis['career_matches']))
        <div class="section">
            <div class="section-title">Rekomendasi Karir</div>
            <div class="career-tags">
                @foreach($analisis['career_matches'] as $career)
                <span class="career-tag">{{ $career }}</span>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Informasi Sertifikat -->
        @if($certificate)
        <div class="certificate-info">
            <div class="certificate-number">
                📜 Sertifikat Nomor: {{ $certificate->nomor_sertifikat }}
            </div>
            <div style="margin-top: 8px; font-size: 12px; color: #2d3748;">
                Diterbitkan pada: {{ $certificate->tanggal_terbit->format('d F Y') }}
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini digenerate secara otomatis oleh Platform Saintara</p>
            <p>© {{ date('Y') }} Saintara - All Rights Reserved</p>
            <p style="margin-top: 10px;">
                Untuk verifikasi sertifikat, kunjungi: www.saintara.com/verify
            </p>
        </div>
    </div>
</body>
</html>
