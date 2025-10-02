@extends('partials.print-layout')

@section('print-content')
@if($data && $data->count() > 0)
<table class="print-table">
    <thead>
        <tr>
            <th class="text-center" style="width: 5%;">No</th>
            <th style="width: 15%;">Nomor Surat</th>
            <th style="width: 12%;">Tanggal Surat</th>
            <th style="width: 12%;">Tanggal Terima</th>
            <th style="width: 20%;">Perihal</th>
            <th style="width: 15%;">Pengirim</th>
            <th style="width: 15%;">Bagian Tujuan</th>
            <th style="width: 9%;">Dibuat Oleh</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $index => $surat)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $surat->nomor_surat }}</td>
            <td class="text-center">{{ $surat->tanggal_surat->format('d-m-Y') }}</td>
            <td class="text-center">{{ $surat->tanggal_terima->format('d-m-Y') }}</td>
            <td>{{ $surat->perihal }}</td>
            <td>{{ $surat->pengirim }}</td>
            <td>{{ $surat->tujuanBagian ? $surat->tujuanBagian->nama_bagian : '-' }}</td>
            <td>{{ $surat->creator_name ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center">Tidak ada data surat masuk</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endif
@endsection
