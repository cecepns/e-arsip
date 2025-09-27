<tbody>
    @forelse($suratKeluar as $index => $surat)
    <tr>
        <td class="text-center">{{ $index + 1 }}</td>
        <td>{{ $surat->nomor_surat }}</td>
        <td>{{ $surat->tanggal_surat->format('d-m-Y') }}</td>
        <td>{{ $surat->tanggal_keluar ? $surat->tanggal_keluar->format('d-m-Y') : '-' }}</td>
        <td>{{ $surat->tujuan }}</td>
        <td>{{ $surat->perihal }}</td>
        <td>
            @if($surat->pengirimBagian)
                {{ $surat->pengirimBagian->nama_bagian }}
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            <div class="action-buttons">
                <button class="action-btn view-btn" title="Lihat" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modalDetailSuratKeluar"
                        onclick="showDetailSuratKeluarModal({{ $surat->id }})">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="action-btn edit-btn" title="Edit" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modalEditSuratKeluar"
                        onclick="showEditSuratKeluarModal({{ $surat->id }})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="action-btn delete-btn" title="Hapus" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modalDeleteSuratKeluar"
                        onclick="showDeleteSuratKeluarModal({{ $surat->id }})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="8" class="text-center">Tidak ada data surat keluar</td>
    </tr>
    @endforelse
</tbody>
