<tbody>
    @forelse($data as $index => $surat)
    <tr>
        <td class="text-center">{{ $index + 1 }}</td>
        <td class="fw-bold text-primary">{{ $surat->nomor_surat }}</td>
        <td>{{ $surat->tanggal_surat->format('d-m-Y') }}</td>
        <td>{{ $surat->tanggal_terima->format('d-m-Y') }}</td>
        <td>{{ $surat->perihal }}</td>
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
        <td class="text-center">
            <button class="btn btn-sm btn-outline-primary" title="Lihat Detail" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalDetailSuratMasuk"
                    onclick="showDetailSuratMasukModal({{ $surat->id }})">
                <i class="fas fa-eye"></i>
            </button>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="10" class="text-center">Tidak ada data surat masuk</td>
    </tr>
    @endforelse
</tbody>
