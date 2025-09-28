<tbody>
    @forelse($disposisi as $index => $item)
    <tr>
        <td class="text-center">{{ $disposisi->firstItem() + $index }}</td>
        <td class="fw-bold text-primary">{{ $item->suratMasuk->nomor_surat ?? '-' }}</td>
        <td>{{ $item->suratMasuk->tanggal_surat ? $item->suratMasuk->tanggal_surat->format('d-m-Y') : '-' }}</td>
        <td>{{ $item->suratMasuk->perihal ?? '-' }}</td>
        <td>
            @if($item->suratMasuk && $item->suratMasuk->tujuanBagian)
                <div class="d-flex flex-column">
                    <span class="fw-semibold text-primary">{{ $item->suratMasuk->tujuanBagian->kepalaBagian->nama ?? 'Belum ditentukan' }}</span>
                    <small class="text-muted">{{ $item->suratMasuk->tujuanBagian->nama_bagian }}</small>
                </div>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            @if($item->tujuanBagian)
                <div class="d-flex flex-column">
                    <span class="fw-semibold text-success">{{ $item->tujuanBagian->kepalaBagian->nama ?? 'Belum ditentukan' }}</span>
                    <small class="text-muted">{{ $item->tujuanBagian->nama_bagian }}</small>
                </div>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            @if($item->suratMasuk && $item->suratMasuk->sifat_surat)
                @php
                    $sifatClass = match($item->suratMasuk->sifat_surat) {
                        'Segera' => 'bg-warning',
                        'Penting' => 'bg-danger',
                        'Rahasia' => 'bg-dark',
                        default => 'bg-secondary'
                    };
                @endphp
                <span class="badge {{ $sifatClass }}">{{ $item->suratMasuk->sifat_surat }}</span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>{{ $item->tanggal_disposisi ? $item->tanggal_disposisi->format('d-m-Y') : '-' }}</td>
        <td>
            @if($item->batas_waktu)
                @php
                    $isOverdue = $item->batas_waktu < now() && $item->status !== 'Selesai';
                    $isNearDeadline = $item->batas_waktu <= now()->addDays(3) && $item->status !== 'Selesai';
                @endphp
                <span class="{{ $isOverdue ? 'text-danger fw-bold' : ($isNearDeadline ? 'text-warning fw-bold' : '') }}">
                    {{ $item->batas_waktu->format('d-m-Y') }}
                </span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            @if($item->isi_instruksi)
                <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $item->isi_instruksi }}">
                    {{ $item->isi_instruksi }}
                </span>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            @php
                $statusClass = match($item->status) {
                    'Menunggu' => 'bg-warning',
                    'Dikerjakan' => 'bg-info',
                    'Selesai' => 'bg-success',
                    default => 'bg-secondary'
                };
            @endphp
            <span class="badge {{ $statusClass }}">{{ $item->status }}</span>
        </td>
        <td>
            <div class="action-buttons">
                <button class="action-btn view-btn" title="Lihat Detail" 
                        onclick="showDisposisiDetail({{ $item->id }})">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="action-btn edit-btn" title="Edit" 
                        onclick="editDisposisi({{ $item->id }})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="action-btn delete-btn" title="Hapus" 
                        onclick="deleteDisposisi({{ $item->id }}, '{{ $item->suratMasuk->nomor_surat ?? 'N/A' }}')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="12" class="text-center py-4">
            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
            <p class="text-muted mb-0">Tidak ada data disposisi</p>
        </td>
    </tr>
    @endforelse
</tbody>
