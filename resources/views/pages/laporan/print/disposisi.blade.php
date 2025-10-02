@extends('partials.print-layout')

@section('print-content')
@if($data && $data->count() > 0)
<table class="print-table">
    <thead>
        <tr>
            <th class="text-center" style="width: 4%;">No</th>
            <th style="width: 12%;">No Surat</th>
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
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $item->suratMasuk ? $item->suratMasuk->nomor_surat : '-' }}</td>
            <td class="text-center">{{ $item->suratMasuk && $item->suratMasuk->tanggal_surat ? $item->suratMasuk->tanggal_surat->format('d-m-Y') : '-' }}</td>
            <td>{{ $item->suratMasuk ? $item->suratMasuk->perihal : '-' }}</td>
            <td>
                @if($item->suratMasuk && $item->suratMasuk->tujuanBagian)
                    {{ $item->suratMasuk->tujuanBagian->nama_bagian }}
                @else
                    -
                @endif
            </td>
            <td>
                @if($item->tujuanBagian)
                    {{ $item->tujuanBagian->nama_bagian }}
                @else
                    -
                @endif
            </td>
            <td class="text-center">
                @if($item->suratMasuk && $item->suratMasuk->sifat_surat)
                    @php
                        $sifatClass = match($item->suratMasuk->sifat_surat) {
                            'Segera' => 'status-segera',
                            'Penting' => 'status-penting',
                            'Rahasia' => 'status-rahasia',
                            default => 'status-biasa'
                        };
                    @endphp
                    <span class="status-badge {{ $sifatClass }}">{{ $item->suratMasuk->sifat_surat }}</span>
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
            <td colspan="9" class="text-center">Tidak ada data disposisi</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endif
@endsection
