@extends('partials.print-layout')

@section('print-content')
@if($data && $data->count() > 0)
<table class="print-table">
    <thead>
        <tr>
            <th class="text-center" style="width: 4%;">No</th>
            <th style="width: 12%;">No Surat</th>
            <th style="width: 8%;">Jenis</th>
            <th style="width: 10%;">Tanggal Surat</th>
            <th style="width: 15%;">Perihal</th>
            <th style="width: 15%;">Disposisi Dari</th>
            <th style="width: 15%;">Disposisi Kepada</th>
            <th style="width: 8%;">Sifat</th>
            <th style="width: 10%;">Tgl Disposisi</th>
            <th style="width: 11%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $index => $item)
        @php
            $suratMasuk = $item->suratMasuk;
            $suratKeluar = $item->suratKeluar;
            $isSuratMasuk = (bool) $suratMasuk;
            $nomorSurat = $isSuratMasuk
                ? ($suratMasuk->nomor_surat ?? '-')
                : ($suratKeluar->nomor_surat ?? '-');
            $tanggalSurat = $isSuratMasuk
                ? ($suratMasuk?->tanggal_surat ? $suratMasuk->tanggal_surat->format('d-m-Y') : '-')
                : ($suratKeluar?->tanggal_surat ? $suratKeluar->tanggal_surat->format('d-m-Y') : '-');
            $perihal = $isSuratMasuk
                ? ($suratMasuk->perihal ?? '-')
                : ($suratKeluar->perihal ?? '-');
            $asalBagian = $isSuratMasuk
                ? ($suratMasuk?->tujuanBagian ?? null)
                : ($suratKeluar?->pengirimBagian ?? null);
            $asalNamaBagian = $asalBagian?->nama_bagian ?? '-';
            $sifatSurat = $isSuratMasuk
                ? ($suratMasuk->sifat_surat ?? null)
                : ($suratKeluar->sifat_surat ?? null);
            $jenisLabel = $isSuratMasuk ? 'Surat Masuk' : 'Surat Keluar';
        @endphp
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $nomorSurat }}</td>
            <td class="text-center">{{ $jenisLabel }}</td>
            <td class="text-center">{{ $tanggalSurat }}</td>
            <td>{{ $perihal }}</td>
            <td>{{ $asalNamaBagian }}</td>
            <td>
                @if($item->tujuanBagian)
                    {{ $item->tujuanBagian->nama_bagian }}
                @else
                    -
                @endif
            </td>
            <td class="text-center">
                @if($sifatSurat)
                    @php
                        $sifatClass = match($sifatSurat) {
                            'Segera' => 'status-segera',
                            'Penting' => 'status-penting',
                            'Rahasia' => 'status-rahasia',
                            default => 'status-biasa'
                        };
                    @endphp
                    <span class="status-badge {{ $sifatClass }}">{{ $sifatSurat }}</span>
                @else
                    -
                @endif
            </td>
            <td class="text-center">{{ $item->tanggal_disposisi ? $item->tanggal_disposisi->format('d-m-Y') : '-' }}</td>
            <td class="text-center">
                @php
                    $statusClass = match($item->status) {
                        'Menunggu' => 'status-menunggu',
                        'Dikerjakan' => 'status-dikerjakan',
                        'Selesai' => 'status-selesai',
                        default => 'status-biasa'
                    };
                @endphp
                <span class="status-badge {{ $statusClass }}">{{ $item->status }}</span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="10" class="text-center">Tidak ada data disposisi</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endif
@endsection
