<tbody>
    @forelse($suratKeluar as $i => $surat)
    <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $surat->nomor_surat }}</td>
        <td>{{ $surat->tanggal_surat->format('d-m-Y') }}</td>
        <td>{{ $surat->tanggal_keluar ? $surat->tanggal_keluar->format('d-m-Y') : '-' }}</td>
        <td>{{ $surat->tujuan }}</td>
        <td>{{ $surat->perihal }}</td>
        <td>{{ $surat->pengirimBagian->nama_bagian ?? '-' }}</td>
        <td>
            <a href="{{ route('surat_keluar.show', $surat->id) }}" class="btn btn-info btn-sm">Detail</a>
            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#formSuratKeluarModal" data-id="{{ $surat->id }}">Edit</button>
            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteSuratKeluarModal" data-id="{{ $surat->id }}">Hapus</button>
        </td>
    </tr>
    @empty
    <tr><td colspan="8">Tidak ada data surat keluar.</td></tr>
    @endforelse
</tbody>
