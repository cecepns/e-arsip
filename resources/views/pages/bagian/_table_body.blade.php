<tbody>
    @forelse($bagian as $index => $item)
    <tr>
        <td class="text-center">{{ $index + 1 }}</td>
        <td>{{ $item->nama_bagian }}</td>
        <td>{{ $item->kepala_bagian }}</td>
        <td class="text-center">
            <span class="badge-incoming mb-1">Masuk: 8</span>
            <br>
            <span class="badge-outgoing">Keluar: 7</span>
        </td>
        <td class="text-center">8</td>
        <td class="text-center">
            @if($item->status == 'Aktif')
                <span class="badge-active">Aktif</span>
            @else
                <span class="badge-inactive">Tidak Aktif</span>
            @endif
        </td>
        <td>
            <div class="action-buttons">
                <button class="action-btn view-btn" title="Lihat" data-bs-toggle="modal" data-bs-target="#modalBagianDetail">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="action-btn edit-btn" title="Edit" data-bs-toggle="modal" data-bs-target="#modalBagianForm">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="action-btn delete-btn" title="Hapus" data-bs-toggle="modal" data-bs-target="#modalDeleteBagian">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="7" class="text-center">Tidak ada data bagian</td>
    </tr>
    @endforelse
</tbody>
