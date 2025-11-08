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
        $asalKepala = $asalBagian?->kepalaBagian->nama ?? 'Belum ditentukan';
        $asalNamaBagian = $asalBagian?->nama_bagian ?? '-';
        $sifatSurat = $isSuratMasuk
            ? ($suratMasuk->sifat_surat ?? null)
            : ($suratKeluar->sifat_surat ?? null);
        $sifatClass = match($sifatSurat ?? '') {
            'Segera' => 'bg-warning',
            'Penting' => 'bg-danger',
            'Rahasia' => 'bg-dark',
            default => 'bg-secondary'
        };
        $jenisLabel = $isSuratMasuk ? 'Surat Masuk' : 'Surat Keluar';
        $jenisClass = $isSuratMasuk ? 'bg-primary' : 'bg-info';
    @endphp
    <tr>
        <td class="text-center">{{ $index + 1 }}</td>
        <td class="fw-bold text-primary">{{ $nomorSurat }}</td>
        <td><span class="badge {{ $jenisClass }}">{{ $jenisLabel }}</span></td>
        <td>{{ $tanggalSurat }}</td>
        <td>{{ $perihal }}</td>
        <td>
            <div class="d-flex flex-column">
                <span class="fw-semibold text-primary">{{ $asalKepala }}</span>
                <small class="text-muted">{{ $asalNamaBagian }}</small>
            </div>
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
            @if($sifatSurat)
                <span class="badge {{ $sifatClass }}">{{ $sifatSurat }}</span>
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
        <td class="text-center">
            <div class="action-buttons">
                <button class="action-btn view-btn" title="Lihat Detail" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalDetailDisposisi"
                    onclick="showDisposisiDetail({{ $item->id }})">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="13" class="text-center py-4">
            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
            <p class="text-muted mb-0">Tidak ada data disposisi</p>
        </td>
    </tr>
    @endforelse
</tbody>
