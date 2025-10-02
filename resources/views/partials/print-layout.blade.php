<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan {{ $jenisLaporan ?? 'Surat' }}</title>
    <style>
        /* ANCHOR: Print Layout Styles */
        @media print {
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                margin: 0;
                padding: 0;
                font-family: 'Times New Roman', serif;
                font-size: 12px;
                line-height: 1.4;
                color: #000;
            }
            
            .kop-surat {
                margin: 0;
                padding: 20px;
                width: 100vw;
                margin-left: calc(-50vw + 50%);
                margin-right: calc(-50vw + 50%);
            }
            
            .no-print {
                display: none !important;
            }
            
            .print-break {
                page-break-before: always;
            }
            
            .print-avoid-break {
                page-break-inside: avoid;
            }
        }
        
        /* ANCHOR: General Styles */
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .container {
            padding: 20px;
        }
        
        .kop-surat {
            display: flex;
            align-items: center;
            margin: 0;
            margin-bottom: 30px;
            padding: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ccc;
            width: 100vw;
            margin-left: calc(-50vw + 50%);
            margin-right: calc(-50vw + 50%);
            min-height: 90px;
            background-color: #fff;
            box-sizing: border-box;
        }
        
        .logo-container {
            margin-right: 30px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo-container img {
            height: 100px;
            width: auto;
            max-width: 100px;
            object-fit: contain;
        }
        
        .instansi-info {
            flex-grow: 1;
            text-align: left;
            padding-top: 5px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 80px;
        }
        
        .instansi-nama {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
            line-height: 1.2;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .instansi-alamat {
            font-size: 14px;
            margin-bottom: 5px;
            color: #666;
            line-height: 1.3;
            font-weight: normal;
        }
        
        .instansi-kontak {
            font-size: 14px;
            color: #666;
            line-height: 1.3;
            font-weight: normal;
        }
        
        .laporan-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            text-transform: uppercase;
        }
        
        .laporan-subtitle {
            text-align: center;
            font-size: 11px;
            margin-bottom: 20px;
            font-style: italic;
        }
        
        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .print-table th,
        .print-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        
        .print-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 11px;
        }
        
        .print-table td {
            font-size: 10px;
        }
        
        .print-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .print-table .text-truncate {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .print-table .text-center {
            text-align: center;
        }
        
        .print-table .text-right {
            text-align: right;
        }
        
        .print-info {
            margin-bottom: 20px;
            font-size: 10px;
        }
        
        .print-info .info-item {
            margin-bottom: 3px;
        }
        
        .print-info .label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        
        .print-footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
        }
        
        .print-actions {
            text-align: center;
            margin: 20px 0;
        }
        
        .print-actions button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 0 5px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .print-actions button:hover {
            background-color: #0056b3;
        }
        
        .print-actions .btn-secondary {
            background-color: #6c757d;
        }
        
        .print-actions .btn-secondary:hover {
            background-color: #545b62;
        }
        
        /* ANCHOR: Status Badge Styles for Print */
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-menunggu {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-dikerjakan {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .status-selesai {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-biasa {
            background-color: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }
        
        .status-segera {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-penting {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status-rahasia {
            background-color: #d1d3d4;
            color: #1b1e21;
            border: 1px solid #c6c8ca;
        }
    </style>
</head>
<body>
    <!-- ANCHOR: Print Actions -->
    @if(isset($showPrintActions) && $showPrintActions)
    <div class="print-actions no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Cetak
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>
    @endif

    <!-- ANCHOR: Kop Surat Full Width -->
    <div class="kop-surat">
        <div class="logo-container">
            @if($pengaturan->logo)
                @if(isset($pengaturan->logo_url))
                    <img src="{{ $pengaturan->logo_url }}" alt="Logo Instansi">
                @else
                    <img src="{{ Storage::url($pengaturan->logo) }}" alt="Logo Instansi">
                @endif
            @else
                <!-- Placeholder untuk logo jika tidak ada -->
                <div style="width: 80px; height: 80px; background-color: #f0f0f0; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">
                    LOGO
                </div>
            @endif
        </div>
        <div class="instansi-info">
            <div class="instansi-nama">{{ $pengaturan->nama_instansi }}</div>
            <div class="instansi-alamat">{{ $pengaturan->alamat }}</div>
            <div class="instansi-kontak">
                @if($pengaturan->no_telp)
                    Telp: {{ $pengaturan->no_telp }}
                @endif
                @if($pengaturan->email)
                    @if($pengaturan->no_telp) | @endif
                    Email: {{ $pengaturan->email }}
                @endif
            </div>
        </div>
    </div>

    <!-- ANCHOR: Content Container -->
    <div class="container">
        <!-- ANCHOR: Laporan Title -->
        <div class="laporan-title">{{ $jenisLaporan }}</div>
        
        <!-- ANCHOR: Laporan Subtitle -->
        <div class="laporan-subtitle">{{ $periodeLaporan }}</div>

        <!-- ANCHOR: Print Info -->
        <div class="print-info">
            <div class="info-item">
                <span class="label">Tanggal Cetak:</span>
                <span>{{ \Carbon\Carbon::now()->format('d F Y, H:i') }}</span>
            </div>
            <div class="info-item">
                <span class="label">Jumlah Data:</span>
                <span>{{ $data->count() }} {{ $jenisData }}</span>
            </div>
            @if(isset($filters['bagian_id']) && $filters['bagian_id'])
                <div class="info-item">
                    <span class="label">Bagian:</span>
                    <span>{{ $selectedBagian->nama_bagian ?? 'Semua Bagian' }}</span>
                </div>
            @endif
        </div>

        <!-- ANCHOR: Table Content -->
        @if($data && $data->count() > 0)
            @yield('print-content')
        @else
            <div class="text-center" style="padding: 40px; font-style: italic; color: #666;">
                <p>Tidak ada data untuk dicetak berdasarkan filter yang dipilih.</p>
            </div>
        @endif

        <!-- ANCHOR: Print Footer -->
        <div class="print-footer">
            <div>Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y, H:i') }}</div>
            <div>Oleh: {{ Auth::user()->nama ?? 'System' }}</div>
        </div>
    </div>
</body>
</html>
