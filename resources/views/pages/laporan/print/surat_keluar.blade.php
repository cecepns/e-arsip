@extends('partials.print-layout')

@section('print-content')
@if($data && $data->count() > 0)
<table class="print-table">
    <thead>
        <tr>
            <th class="text-center" style="width: 5%;">No</th>
            <th style="width: 15%;">Nomor Surat</th>
            <th style="width: 12%;">Tanggal Surat</th>
            <th style="width: 12%;">Tanggal Keluar</th>
            <th style="width: 15%;">Penerima</th>
            <th style="width: 10%;">Sifat Surat</th>
            <th style="width: 18%;">Perihal</th>
            <th style="width: 13%;">Bagian Pengirim</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $index => $surat)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $surat->nomor_surat }}</td>
            <td class="text-center">{{ $surat->tanggal_surat->format('d-m-Y') }}</td>
            <td class="text-center">{{ $surat->tanggal_keluar ? $surat->tanggal_keluar->format('d-m-Y') : '-' }}</td>
            <td>{{ $surat->tujuan }}</td>
            <td class="text-center">
                @php
                    $sifatSurat = $surat->sifat_surat ?? 'Biasa';
                    $statusClass = match($sifatSurat) {
                        'Segera' => 'status-segera',
                        'Penting' => 'status-penting',
                        'Rahasia' => 'status-rahasia',
                        default => 'status-biasa'
                    };
                @endphp
                <span class="status-badge {{ $statusClass }}">{{ $sifatSurat }}</span>
            </td>
            <td>{{ $surat->perihal }}</td>
            <td>{{ $surat->pengirimBagian ? $surat->pengirimBagian->nama_bagian : '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center">Tidak ada data surat keluar</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endif
@endsection
