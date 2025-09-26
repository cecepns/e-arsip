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
            <div class="action-buttons">
                <button class="action-btn view-btn" title="Lihat" 
                        onclick="window.location.href='{{ route('surat_keluar.show', $surat->id) }}'">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="action-btn edit-btn" title="Edit" 
                        data-bs-toggle="modal" 
                        data-bs-target="#formSuratKeluarModal"
                        data-id="{{ $surat->id }}">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="action-btn delete-btn" title="Hapus" 
                        data-bs-toggle="modal" 
                        data-bs-target="#deleteSuratKeluarModal"
                        data-id="{{ $surat->id }}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
    @empty
    <tr><td colspan="8">Tidak ada data surat keluar.</td></tr>
    @endforelse
</tbody>
