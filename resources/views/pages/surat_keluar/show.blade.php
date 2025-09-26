@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Detail Surat Keluar</h2>
    <table class="table">
        <tr><th>Nomor Surat</th><td>{{ $suratKeluar->nomor_surat }}</td></tr>
        <tr><th>Tanggal Surat</th><td>{{ $suratKeluar->tanggal_surat->format('d-m-Y') }}</td></tr>
        <tr><th>Tanggal Keluar</th><td>{{ $suratKeluar->tanggal_keluar ? $suratKeluar->tanggal_keluar->format('d-m-Y') : '-' }}</td></tr>
        <tr><th>Penerima</th><td>{{ $suratKeluar->tujuan }}</td></tr>
        <tr><th>Perihal</th><td>{{ $suratKeluar->perihal }}</td></tr>
        <tr><th>Ringkasan Isi</th><td>{{ $suratKeluar->ringkasan_isi }}</td></tr>
        <tr><th>Bagian Pengirim</th><td>{{ $suratKeluar->pengirimBagian->nama_bagian ?? '-' }}</td></tr>
        <tr><th>Keterangan</th><td>{{ $suratKeluar->keterangan }}</td></tr>
        <tr><th>Lampiran</th><td>
            @if($suratKeluar->lampiran)
                <a href="{{ asset('storage/' . $suratKeluar->lampiran) }}" target="_blank">Unduh Lampiran</a>
            @else
                -
            @endif
        </td></tr>
    </table>
    <a href="{{ route('surat_keluar.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection