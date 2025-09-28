<tbody>
    @forelse($suratMasuk as $index => $surat)
    <tr>
        <td class="text-center">{{ $index + 1 }}</td>
        <td class="fw-bold text-primary">{{ $surat->nomor_surat }}</td>
        <td>{{ $surat->tanggal_surat->format('d-m-Y') }}</td>
        <td>{{ $surat->tanggal_terima->format('d-m-Y') }}</td>
        <td>{{ $surat->perihal }}</td>
        <td>
            @if($surat->ringkasan_isi)
                {{ Str::limit($surat->ringkasan_isi, 50) }}
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>{{ $surat->pengirim }}</td>
        <td>
            @if($surat->tujuanBagian)
                {{ $surat->tujuanBagian->nama_bagian }}
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            <small class="text-muted">
                @if($surat->creator)
                    {{ $surat->creator_name }}
                    <br>
                    <span class="text-muted">{{ $surat->created_at->format('d-m-Y H:i') }}</span>
                @else
                    <span class="text-muted">-</span>
                @endif
            </small>
        </td>
        <td>
            <small class="text-muted">
                @if($surat->updater)
                    {{ $surat->updater_name }}
                    <br>
                    <span class="text-muted">{{ $surat->updated_at->format('d-m-Y H:i') }}</span>
                @else
                    <span class="text-muted">-</span>
                @endif
            </small>
        </td>
        <td>
            <div class="action-buttons">
                <button class="action-btn view-btn" title="Lihat" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modalDetailSuratMasuk"
                        onclick="showDetailSuratMasukModal({{ $surat->id }})">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="action-btn edit-btn" title="Edit" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modalEditSuratMasuk"
                        onclick="showEditSuratMasukModal({{ $surat->id }})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="action-btn delete-btn" title="Hapus" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modalDeleteSuratMasuk"
                        onclick="showDeleteSuratMasukModal({{ $surat->id }})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="11" class="text-center">Tidak ada data surat masuk</td>
    </tr>
    @endforelse
</tbody>
