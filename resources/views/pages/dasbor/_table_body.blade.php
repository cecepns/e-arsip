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
                    <button class="action-btn view-btn" title="Lihat" data-bs-toggle="modal" data-bs-target="#detailModal">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn delete-btn" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
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
