<tbody>
    @forelse($recentActivity as $index => $activity)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td class="text-primary"><strong>{{ $activity['nomor_surat'] }}</strong></td>
            <td>{{ $activity['tanggal_surat'] ? \Carbon\Carbon::parse($activity['tanggal_surat'])->format('d F Y') : '-' }}</td>
            <td>{{ $activity['perihal'] }}</td>
            <td>
                <span class="badge-{{ $activity['jenis'] == 'Surat Masuk' ? 'incoming' : 'outgoing' }}">
                    {{ $activity['jenis'] }}
                </span>
            </td>
            <td>
                <div class="action-buttons">
                    @if($activity['jenis'] == 'Surat Masuk')
                        <button class="action-btn view-btn" title="Lihat Detail" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalDetailSuratMasuk"
                                onclick="showDetailSuratMasukModal({{ $activity['id'] }})">
                            <i class="fas fa-eye"></i>
                        </button>
                    @elseif($activity['jenis'] == 'Surat Keluar')
                        <button class="action-btn view-btn" title="Lihat Detail" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalDetailSuratKeluar"
                                onclick="showDetailSuratKeluarModal({{ $activity['id'] }})">
                            <i class="fas fa-eye"></i>
                        </button>
                    @else
                        <button class="action-btn view-btn" title="Lihat Detail" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalDetailDisposisi"
                                onclick="showDisposisiDetail({{ $activity['id'] }})">
                            <i class="fas fa-eye"></i>
                        </button>
                    @endif
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted">
                <i class="fas fa-inbox fa-2x mb-2"></i>
                <br>
                Belum ada aktivitas surat terbaru
            </td>
        </tr>
    @endforelse
</tbody>
